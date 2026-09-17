<?php

class ParticipantModel extends Model
{
    public function getAllPaginated(int $page = 1, array $filters = []): array
    {
        $where = ['1=1']; $params = [];
        if (!empty($filters['barangay_id'])) { $where[] = 'p.barangay_id=:bid'; $params[':bid'] = $filters['barangay_id']; }
        if (!empty($filters['category']))    { $where[] = 'p.category=:cat';    $params[':cat'] = $filters['category']; }
        if (!empty($filters['search']))      { $where[] = "(p.first_name LIKE :s OR p.last_name LIKE :s OR p.contact_no LIKE :s)"; $params[':s'] = '%'.$filters['search'].'%'; }

        $sql = "SELECT p.*, b.name AS barangay_name FROM participants p
                JOIN barangays b ON b.id = p.barangay_id
                WHERE ".implode(' AND ',$where)." ORDER BY p.last_name, p.first_name";
        return $this->paginate($sql, $params, $page);
    }

    public function findById(int $id): array|false
    {
        return $this->fetchOne("SELECT p.*, b.name AS barangay_name FROM participants p JOIN barangays b ON b.id=p.barangay_id WHERE p.id=:id", [':id'=>$id]);
    }

    public function create(array $d): int|string
    {
        return $this->insertAndGetId(
            "INSERT INTO participants (barangay_id,first_name,last_name,birthdate,gender,civil_status,address,contact_no,category)
             VALUES (:barangay_id,:first_name,:last_name,:birthdate,:gender,:civil_status,:address,:contact_no,:category)", $d);
    }

    public function update(int $id, array $d): int
    {
        return $this->execute(
            "UPDATE participants SET barangay_id=:barangay_id,first_name=:first_name,last_name=:last_name,
             birthdate=:birthdate,gender=:gender,civil_status=:civil_status,address=:address,
             contact_no=:contact_no,category=:category WHERE id=:id", array_merge($d,[':id'=>$id]));
    }

    public function getBarangays(): array { return $this->fetchAll("SELECT id,name FROM barangays WHERE is_adopted=1 ORDER BY name"); }

    public function getActivityHistory(int $participantId): array
    {
        return $this->fetchAll(
            "SELECT att.*, a.title AS activity_title, a.activity_date, pp.title AS project_title
             FROM attendance att JOIN activities a ON a.id=att.activity_id
             JOIN project_proposals pp ON pp.id=a.proposal_id
             WHERE att.participant_id=:id AND att.attendee_type='participant'
             ORDER BY a.activity_date DESC", [':id'=>$participantId]);
    }
}
