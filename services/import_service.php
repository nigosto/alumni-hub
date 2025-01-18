<?php
class ImportService
{
    public function parse_csv_as_base64($file)
    {
        $csv_content = base64_decode($file);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $csv_content);
        rewind($stream);

        $rows = [];
        while (($row = fgetcsv($stream)) !== false) {
            $rows[] = $row;
        }

        $header = $rows[0];
        array_shift($rows);

        return [
            "header" => $header,
            "data" => array_map(function ($row) use ($header) {
                return array_combine($header, $row);
            }, array_map(function ($row) {
                return array_filter($row, function ($item) {
                    return !empty(strval($item));
            });}, $rows))
        ];
    }
}
?>