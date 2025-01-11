<?php
class ImportService
{
    public function parse_csv_as_base64($file)
    {
        $file_contents = base64_decode($file);
        $rows = explode("\n", $file_contents);

        $header = explode(",", $rows[0]);
        array_shift($rows);

        return [
            "header" => $header,
            "data" => array_map(function ($row) use ($header) {
                return array_combine($header, $row);
            }, array_filter(array_map(function ($row) {
                return str_getcsv($row);
            }, $rows), function ($row) use ($header) {
                return count($row) === count($header);
            }))
        ];
    }
}
?>