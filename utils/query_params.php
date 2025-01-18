<?php
function parse_query_params() {
    $query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    $query_params = [];
    parse_str($query_string, $query_params);
    return $query_params;
}
?>