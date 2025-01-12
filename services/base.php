<?php
class BaseDataService
{
    private $connection;

    function __construct(Database $database)
    {
        $this->connection = $database->connection();
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

    function find_all_with_query($query, $class, $data = null) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($data);

        try {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $i => $row) {
                $rows[$i] = new $class(...array_values($row));
            }

            return $rows;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>