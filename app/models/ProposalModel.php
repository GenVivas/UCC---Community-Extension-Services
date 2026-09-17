<?php

class ProposalModel extends Model
{
    public function getAllPaginated(int $page = 1, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = 'pp.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['program_id'])) {
            $where[]              = 'pp.program_id = :program_id';
            $params[':program_id'] = $filters['program_id'];
        }
        if (!empty($filters['search'])) {
            $where[]           = 'pp.title LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT pp.id, pp.title, pp.status, pp.target_area, pp.start_date, pp.end_date,
                       pp.budget_requested, pp.created_at,
                       u.first_name, u.last_name,
                       p.code AS program_code, p.name AS program_name,
                       b.name AS barangay_name
                FROM project_proposals pp
                JOIN users u ON u.id = pp.submitted_by
                JOIN programs p ON p.id = pp.program_id
                JOIN barangays b ON b.id = pp.barangay_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY pp.created_at DESC";

        return $this->paginate($sql, $params, $page);
    }

    public function findById(int $id): array|false
    {
        return $this->fetchOne(
            "SELECT pp.*, u.first_name, u.last_name, u.email AS submitter_email,
                    p.code AS program_code, p.name AS program_name,
                    b.name AS barangay_name,
                    a.first_name AS approver_first, a.last_name AS approver_last,
                    cna.title AS cna_title
             FROM project_proposals pp
             JOIN users u ON u.id = pp.submitted_by
             JOIN programs p ON p.id = pp.program_id
             JOIN barangays b ON b.id = pp.barangay_id
             LEFT JOIN users a ON a.id = pp.approved_by
             LEFT JOIN community_needs_assessments cna ON cna.id = pp.assessment_id
             WHERE pp.id = :id",
            [':id' => $id]
        );
    }

    public function create(array $data): int|string
    {
        return $this->insertAndGetId(
            "INSERT INTO project_proposals
             (assessment_id, barangay_id, program_id, submitted_by, title, description,
              objectives, target_area, target_beneficiaries, expected_output,
              start_date, end_date, budget_requested, attachment, status)
             VALUES
             (:assessment_id, :barangay_id, :program_id, :submitted_by, :title, :description,
              :objectives, :target_area, :target_beneficiaries, :expected_output,
              :start_date, :end_date, :budget_requested, :attachment, :status)",
            $data
        );
    }

    public function update(int $id, array $data): int
    {
        return $this->execute(
            "UPDATE project_proposals
             SET title=:title, description=:description, objectives=:objectives,
                 target_area=:target_area, target_beneficiaries=:target_beneficiaries,
                 expected_output=:expected_output, start_date=:start_date, end_date=:end_date,
                 budget_requested=:budget_requested, barangay_id=:barangay_id,
                 program_id=:program_id, status=:status
             WHERE id=:id",
            array_merge($data, [':id' => $id])
        );
    }

    public function updateStatus(int $id, string $status, ?string $reason = null): int
    {
        return $this->execute(
            "UPDATE project_proposals SET status=:status, rejection_reason=:reason WHERE id=:id",
            [':status' => $status, ':reason' => $reason, ':id' => $id]
        );
    }

    public function approve(int $id, int $approverId): int
    {
        return $this->execute(
            "UPDATE project_proposals SET status='approved', approved_by=:approver, approved_at=NOW() WHERE id=:id",
            [':approver' => $approverId, ':id' => $id]
        );
    }

    public function logApproval(array $data): void
    {
        $this->execute(
            "INSERT INTO proposal_approvals (proposal_id, approver_id, approver_role, action, remarks)
             VALUES (:proposal_id, :approver_id, :approver_role, :action, :remarks)",
            $data
        );
    }

    public function getApprovalHistory(int $proposalId): array
    {
        return $this->fetchAll(
            "SELECT pa.*, u.first_name, u.last_name
             FROM proposal_approvals pa
             JOIN users u ON u.id = pa.approver_id
             WHERE pa.proposal_id = :id
             ORDER BY pa.acted_at DESC",
            [':id' => $proposalId]
        );
    }

    public function delete(int $id): int
    {
        return $this->execute("DELETE FROM project_proposals WHERE id=:id", [':id' => $id]);
    }

    public function getPrograms(): array
    {
        return $this->fetchAll("SELECT id, code, name FROM programs WHERE is_active=1 ORDER BY name");
    }

    public function getBarangays(): array
    {
        return $this->fetchAll("SELECT id, name FROM barangays WHERE is_adopted=1 ORDER BY name");
    }

    public function getApprovedCNAs(): array
    {
        return $this->fetchAll(
            "SELECT cna.id, cna.title, b.name AS barangay_name
             FROM community_needs_assessments cna
             JOIN barangays b ON b.id = cna.barangay_id
             WHERE cna.status = 'approved'
             ORDER BY cna.assessment_date DESC"
        );
    }

    // Activities under a proposal
    public function getActivities(int $proposalId): array
    {
        return $this->fetchAll(
            "SELECT * FROM activities WHERE proposal_id=:id ORDER BY activity_date",
            [':id' => $proposalId]
        );
    }
}
