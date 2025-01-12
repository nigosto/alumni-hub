<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/meta/index.php";
require_once __DIR__ . "/../../components/header/index.php";
require_once __DIR__ . "/../../components/footer/index.php";

$header = new HeaderComponent();
$footer = new FooterComponent();

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets()
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    echo $footer->render();
    ?>
</body>

</html>