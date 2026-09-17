<?php

/**
 * Dashboard Model
 * Aggregated stats per role for dashboard pages.
 */
class DashboardModel extends Model
{
    /** Stats for admin + ces_head */
    public function getAdminStats(): array
    {
        $db = $this->db;
        return [
            'total_proposals'    => (int)$db->query("SELECT COUNT(*) FROM project_proposals")->fetchColumn(),
            'pending_proposals'  => (int)$db->query("SELECT COUNT(*) FROM project_proposals WHERE status='pending'")->fetchColumn(),
            'ongoing_projects'   => (int)$db->query("SELECT COUNT(*) FROM project_proposals WHERE status='ongoing'")->fetchColumn(),
            'completed_projects' => (int)$db->query("SELECT COUNT(*) FROM project_proposals WHERE status='completed'")->fetchColumn(),
            'total_participants' => (int)$db->query("SELECT COUNT(*) FROM participants WHERE is_active=1")->fetchColumn(),
            'total_users'        => (int)$db->query("SELECT COUNT(*) FROM users WHERE is_active=1")->fetchColumn(),
            'total_activities'   => (int)$db->query("SELECT COUNT(*) FROM activities")->fetchColumn(),
            'pending_certs'      => (int)$db->query("SELECT COUNT(*) FROM certificates WHERE is_released=0")->fetchColumn(),
            'total_linkages'     => (int)$db->query("SELECT COUNT(*) FROM linkages WHERE is_active=1")->fetchColumn(),
        ];
    }

    /** Recent proposals (last 8) */
    public function getRecentProposals(int $limit = 8): array
    {
        return $this->fetchAll(
            "SELECT pp.id, pp.title, pp.status, pp.created_at, pp.target_area,
                    u.first_name, u.last_name, p.code AS program_code
             FROM project_proposals pp
             JOIN users u ON u.id = pp.submitted_by
             JOIN programs p ON p.id = pp.program_id
             ORDER BY pp.created_at DESC
             LIMIT :lim",
            [':lim' => $limit]
        );
    }

    /** Upcoming activities (next 5) */
    public function getUpcomingActivities(int $limit = 5): array
    {
        return $this->fetchAll(
            "SELECT a.id, a.title, a.activity_date, a.venue, a.status,
                    pp.title AS project_title
             FROM activities a
             JOIN project_proposals pp ON pp.id = a.proposal_id
             WHERE a.activity_date >= CURDATE() AND a.status != 'cancelled'
             ORDER BY a.activity_date ASC
             LIMIT :lim",
            [':lim' => $limit]
        );
    }

    /** Stats for faculty/program_head scoped to their program */
    public function getProgramStats(int $programId): array
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT COUNT(*) FROM project_proposals WHERE program_id = :pid");
        $stmt->execute([':pid' => $programId]);
        $total = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM project_proposals WHERE program_id = :pid AND status='ongoing'");
        $stmt->execute([':pid' => $programId]);
        $ongoing = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM project_proposals WHERE program_id = :pid AND status='completed'");
        $stmt->execute([':pid' => $programId]);
        $completed = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM project_proposals WHERE program_id = :pid AND status='pending'");
        $stmt->execute([':pid' => $programId]);
        $pending = (int)$stmt->fetchColumn();

        return compact('total', 'ongoing', 'completed', 'pending');
    }

    /** Stats visible to students */
    public function getStudentStats(int $userId): array
    {
        $db = $this->db;

        $stmt = $db->prepare(
            "SELECT COUNT(DISTINCT a.activity_id) FROM attendance a WHERE a.user_id = :uid AND a.attendee_type = 'user'"
        );
        $stmt->execute([':uid' => $userId]);
        $activitiesJoined = (int)$stmt->fetchColumn();

        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM certificates WHERE user_id = :uid AND attendee_type = 'user'"
        );
        $stmt->execute([':uid' => $userId]);
        $certificates = (int)$stmt->fetchColumn();

        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM evaluations WHERE user_id = :uid AND evaluator_type = 'user'"
        );
        $stmt->execute([':uid' => $userId]);
        $evaluations = (int)$stmt->fetchColumn();

        return compact('activitiesJoined', 'certificates', 'evaluations');
    }

    /** Projects per area (for chart) */
    public function getProjectsByArea(): array
    {
        return $this->fetchAll(
            "SELECT target_area AS area, COUNT(*) AS total
             FROM project_proposals
             GROUP BY target_area
             ORDER BY total DESC"
        );
    }

    /** Monthly activity count for current year (for chart) */
    public function getMonthlyActivities(): array
    {
        return $this->fetchAll(
            "SELECT MONTH(activity_date) AS month, COUNT(*) AS total
             FROM activities
             WHERE YEAR(activity_date) = YEAR(CURDATE())
             GROUP BY MONTH(activity_date)
             ORDER BY month"
        );
    }
}
