<?php
class ExportService {
    public function export_as_csv($header, $data) {
        $output = fopen("php://output", "w");
        fputcsv($output, $header);

        foreach($data as $entry) {
            fputcsv($output, $entry);
        }

        fclose($output);
    }
}
?>