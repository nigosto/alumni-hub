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

    function insert_many_bulk_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
    }

    function insert_many_with_query($query, $data)
    {
        return $this->execute_in_transaction(function() use ($query, $data) {
            $stmt = $this->connection->prepare($query);

            foreach ($data as $entry) {
                $stmt->execute($entry->to_array());
            }
        });
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

    function insert_with_query_direct($query, $data)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
        return $this->connection->lastInsertId();
    }

    function insert_with_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);

        $stmt->execute($data->to_array());
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
        if ($row === null || $row === false) {
            return null;
        }
        return new $this->model(...array_values($row));
    }

    function get_with_query_map($query, $data, $map_func)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $row = $map_func($row);

        if ($row === null || $row === false) {
            return null;
        }

        return $row;
    }

    function find_all_with_query_map($query, $data, $map_func)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $i => $row) {
            $rows[$i] = $map_func($row);
        }

        return $rows;
    }

    function delete_with_query($query, $data)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);
    }

    function execute_in_transaction($exec_func)
    {
        $is_in_active_transaction = $this->connection->inTransaction();
        if (!$is_in_active_transaction)
        {
            $this->connection->beginTransaction();
        }

        try {
            $exec_func();
        } catch (PDOException $e) {
            if(!$is_in_active_transaction) {
                $this->connection->rollBack();
            }
            throw $e;
        }

        if(!$is_in_active_transaction) {
            $this->connection->commit();
        }
    }
}
?>