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

        foreach ($data as $entry) {
            $stmt->execute($entry->to_insert_array());
        }
    }
}
?>