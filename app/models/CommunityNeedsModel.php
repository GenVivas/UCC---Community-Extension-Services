<?php

class CommunityNeedsModel extends Model
{
    // ── Assessments ──────────────────────────────────────────

    public function getAllPaginated(int $page = 1, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['barangay_id'])) {
            $where[]              = 'cna.barangay_id = :barangay_id';
            $params[':barangay_id'] = $filters['barangay_id'];
        }
        if (!empty($filters['status'])) {
            $where[]           = 'cna.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]           = 'cna.title LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT cna.*, b.name AS barangay_name,
                       u.first_name, u.last_name,
                       COUNT(DISTINCT cni.id) AS item_count
                FROM community_needs_assessments cna
                JOIN barangays b ON b.id = cna.barangay_id
                JOIN users u ON u.id = cna.conducted_by
                LEFT JOIN community_needs_items cni ON cni.assessment_id = cna.id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY cna.id
                ORDER BY cna.created_at DESC";

        return $this->paginate($sql, $params, $page);
    }

    public function findById(int $id): array|false
    {
        return $this->fetchOne(
            "SELECT cna.*, b.name AS barangay_name,
                    u.first_name, u.last_name,
                    a.first_name AS approver_first, a.last_name AS approver_last
             FROM community_needs_assessments cna
             JOIN barangays b ON b.id = cna.barangay_id
             JOIN users u ON u.id = cna.conducted_by
             LEFT JOIN users a ON a.id = cna.approved_by
             WHERE cna.id = :id",
            [':id' => $id]
        );
    }

    public function create(array $data): int|string
    {
        return $this->insertAndGetId(
            "INSERT INTO community_needs_assessments
             (barangay_id, conducted_by, assessment_date, title, description, status)
             VALUES (:barangay_id, :conducted_by, :assessment_date, :title, :description, :status)",
            [
                ':barangay_id'      => $data['barangay_id'],
                ':conducted_by'     => $data['conducted_by'],
                ':assessment_date'  => $data['assessment_date'],
                ':title'            => $data['title'],
                ':description'      => $data['description'],
                ':status'           => $data['status'] ?? 'draft',
            ]
        );
    }

    public function update(int $id, array $data): int
    {
        return $this->execute(
            "UPDATE community_needs_assessments
             SET barangay_id=:barangay_id, assessment_date=:assessment_date,
                 title=:title, description=:description, status=:status
             WHERE id=:id",
            [
                ':barangay_id'     => $data['barangay_id'],
                ':assessment_date' => $data['assessment_date'],
                ':title'           => $data['title'],
                ':description'     => $data['description'],
                ':status'          => $data['status'],
                ':id'              => $id,
            ]
        );
    }

    public function approve(int $id, int $approverId): int
    {
        return $this->execute(
            "UPDATE community_needs_assessments
             SET status='approved', approved_by=:approver, approved_at=NOW()
             WHERE id=:id",
            [':approver' => $approverId, ':id' => $id]
        );
    }

    public function delete(int $id): int
    {
        return $this->execute("DELETE FROM community_needs_assessments WHERE id=:id", [':id' => $id]);
    }

    // ── Needs Items ──────────────────────────────────────────

    public function getItems(int $assessmentId): array
    {
        return $this->fetchAll(
            "SELECT * FROM community_needs_items WHERE assessment_id=:id ORDER BY severity DESC, id",
            [':id' => $assessmentId]
        );
    }

    public function addItem(array $data): int|string
    {
        return $this->insertAndGetId(
            "INSERT INTO community_needs_items
             (assessment_id, area, problem, severity, recommended_action)
             VALUES (:assessment_id, :area, :problem, :severity, :recommended_action)",
            [
                ':assessment_id'       => $data['assessment_id'],
                ':area'                => $data['area'],
                ':problem'             => $data['problem'],
                ':severity'            => $data['severity'] ?? 'medium',
                ':recommended_action'  => $data['recommended_action'] ?? null,
            ]
        );
    }

    public function deleteItem(int $itemId): int
    {
        return $this->execute("DELETE FROM community_needs_items WHERE id=:id", [':id' => $itemId]);
    }

    public function deleteAllItems(int $assessmentId): void
    {
        $this->execute("DELETE FROM community_needs_items WHERE assessment_id=:id", [':id' => $assessmentId]);
    }

    // ── Helpers ──────────────────────────────────────────────

    public function getBarangays(): array
    {
        return $this->fetchAll("SELECT id, name FROM barangays WHERE is_adopted=1 ORDER BY name");
    }
}
