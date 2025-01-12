<?php
class BaseDataService
{
    private $connection;
    private $model;

    function __construct(Database $database, $model)
    {
        $this->connection = $database->connection();
        $this->model = $model;
    }

    function insert_many_with_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);

        $this->connection->beginTransaction();

        try {
            foreach ($data as $entry) {
                $stmt->execute($entry->to_array());
            }
        } catch (PDOException $e) {
            $this->connection->rollBack();

            throw $e;
        }

        $this->connection->commit();
    }

    function find_all_with_query($query, $data = null) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $i => $row) {
            $rows[$i] = new $this->model(...array_values($row));
        }

        return $rows;
    }
}
?>