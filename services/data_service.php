<?php
class DataService
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

        foreach ($data as $entry) {
            $stmt->execute($entry->to_insert_array());
        }
    }

    function insert_with_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);

        $stmt->execute($data->to_insert_array());
        return $this->connection->lastInsertId();
    }
    function update_with_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
    }
    function get_with_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new $this->model(...array_values($row));
    }

    function find_all_with_query($query, $data = null)
    {
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