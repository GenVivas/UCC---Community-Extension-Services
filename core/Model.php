<?php

/**
 * Base Model
 * Provides a shared PDO connection and common query helpers.
 * All models extend this class.
 */
class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Query Helpers ────────────────────────────────────────

    /**
     * Fetch all rows for a query.
     *
     * @param string $sql    Parameterized SQL
     * @param array  $params Bound parameters
     * @return array
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single row.
     */
    protected function fetchOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Execute an INSERT/UPDATE/DELETE statement.
     * Returns the number of affected rows.
     */
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Execute an INSERT and return the new record's ID.
     */
    protected function insertAndGetId(string $sql, array $params = []): int|string
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->db->lastInsertId();
    }

    /**
     * Count rows matching a condition.
     */
    protected function count(string $table, string $where = '1', array $params = []): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$table}` WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Simple paginated SELECT.
     *
     * @return array ['data' => [...], 'total' => int, 'pages' => int]
     */
    protected function paginate(
        string $sql,
        array  $params,
        int    $page    = 1,
        int    $perPage = PER_PAGE
    ): array {
        // Count total
        $countSql  = "SELECT COUNT(*) FROM ({$sql}) AS _count_query";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch page
        $offset    = ($page - 1) * $perPage;
        $pagedSql  = "{$sql} LIMIT :limit OFFSET :offset";
        $stmt      = $this->db->prepare($pagedSql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'    => $stmt->fetchAll(),
            'total'   => $total,
            'pages'   => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
    }

    /** Begin a transaction. */
    protected function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    /** Commit a transaction. */
    protected function commit(): void
    {
        $this->db->commit();
    }

    /** Roll back a transaction. */
    protected function rollback(): void
    {
        $this->db->rollBack();
    }
}
