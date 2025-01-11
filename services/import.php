<?php
class ImportService {
    public function parse_base64_to_csv($file) {
        $file_contents = base64_decode($file);
        $rows = explode("\n", $file_contents);
        
        $header = explode(",", $rows[0]);

        return array_map(function($row) {
            return explode(",", $row);
        }, $rows);
    }
}

$import_service = new ImportService();
?>