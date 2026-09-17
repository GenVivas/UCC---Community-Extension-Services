<?php

/**
 * User Model
 * Handles all database operations for the users table.
 */
class UserModel extends Model
{
    protected string $table = 'users';

    // ── Auth ─────────────────────────────────────────────────

    /**
     * Find a user by email (for login).
     */
    public function findByEmail(string $email): array|false
    {
        return $this->fetchOne(
            "SELECT u.*, p.name AS program_name
             FROM users u
             LEFT JOIN programs p ON p.id = u.program_id
             WHERE u.email = :email AND u.is_active = 1
             LIMIT 1",
            [':email' => $email]
        );
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): array|false
    {
        return $this->fetchOne(
            "SELECT u.*, p.name AS program_name
             FROM users u
             LEFT JOIN programs p ON p.id = u.program_id
             WHERE u.id = :id
             LIMIT 1",
            [':id' => $id]
        );
    }

    /**
     * Update the last_login timestamp.
     */
    public function updateLastLogin(int $userId): void
    {
        $this->execute(
            "UPDATE users SET last_login = NOW() WHERE id = :id",
            [':id' => $userId]
        );
    }

    // ── Registration ─────────────────────────────────────────

    /**
     * Check if an email is already taken.
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Register a new user (student/faculty self-registration).
     * Returns the new user ID.
     */
    public function register(array $data): int|string
    {
        return $this->insertAndGetId(
            "INSERT INTO users (program_id, employee_id, first_name, last_name, email, password, role, contact_no)
             VALUES (:program_id, :employee_id, :first_name, :last_name, :email, :password, :role, :contact_no)",
            [
                ':program_id'   => $data['program_id']   ?? null,
                ':employee_id'  => $data['employee_id']  ?? null,
                ':first_name'   => $data['first_name'],
                ':last_name'    => $data['last_name'],
                ':email'        => $data['email'],
                ':password'     => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                ':role'         => $data['role'] ?? 'student',
                ':contact_no'   => $data['contact_no'] ?? null,
            ]
        );
    }

    // ── CRUD ─────────────────────────────────────────────────

    /**
     * Get all users with optional filters and pagination.
     */
    public function getAllPaginated(int $page = 1, array $filters = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['role'])) {
            $where[]         = 'u.role = :role';
            $params[':role'] = $filters['role'];
        }

        if (!empty($filters['search'])) {
            $where[]           = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT u.id, u.employee_id, u.first_name, u.last_name, u.email,
                       u.role, u.is_active, u.last_login, u.created_at,
                       p.name AS program_name
                FROM users u
                LEFT JOIN programs p ON p.id = u.program_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY u.created_at DESC";

        return $this->paginate($sql, $params, $page);
    }

    /**
     * Create a new user (admin).
     */
    public function create(array $data): int|string
    {
        return $this->register($data);
    }

    /**
     * Update a user record.
     */
    public function update(int $id, array $data): int
    {
        $fields = [
            'first_name'  => ':first_name',
            'last_name'   => ':last_name',
            'email'       => ':email',
            'role'        => ':role',
            'program_id'  => ':program_id',
            'employee_id' => ':employee_id',
            'contact_no'  => ':contact_no',
            'is_active'   => ':is_active',
        ];

        $params = [':id' => $id];
        $setClauses = [];

        foreach ($fields as $col => $placeholder) {
            if (array_key_exists($col, $data)) {
                $setClauses[] = "`{$col}` = {$placeholder}";
                $params[$placeholder] = $data[$col];
            }
        }

        if (empty($setClauses)) return 0;

        return $this->execute(
            "UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = :id",
            $params
        );
    }

    /**
     * Update a user's password.
     */
    public function updatePassword(int $id, string $newPassword): int
    {
        return $this->execute(
            "UPDATE users SET password = :password WHERE id = :id",
            [
                ':password' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
                ':id'       => $id,
            ]
        );
    }

    /**
     * Soft-delete (deactivate) a user.
     */
    public function deactivate(int $id): int
    {
        return $this->execute(
            "UPDATE users SET is_active = 0 WHERE id = :id",
            [':id' => $id]
        );
    }

    // ── Stats for Dashboard / Login Page ─────────────────────

    /**
     * Get system-wide summary stats displayed on the login page.
     */
    public function getLoginStats(): array
    {
        $db = $this->db;

        $activeProjects = (int) $db->query(
            "SELECT COUNT(*) FROM project_proposals WHERE status IN ('approved','ongoing')"
        )->fetchColumn();

        $volunteers = (int) $db->query(
            "SELECT COUNT(*) FROM users WHERE role IN ('faculty','student') AND is_active = 1"
        )->fetchColumn();

        $partners = (int) $db->query(
            "SELECT COUNT(*) FROM linkages WHERE is_active = 1"
        )->fetchColumn();

        $beneficiaries = (int) $db->query(
            "SELECT COUNT(*) FROM participants WHERE is_active = 1"
        )->fetchColumn();

        return compact('activeProjects', 'volunteers', 'partners', 'beneficiaries');
    }

    // ── Programs list for registration dropdown ───────────────

    public function getPrograms(): array
    {
        return $this->fetchAll("SELECT id, code, name FROM programs WHERE is_active = 1 ORDER BY name");
    }
}
