<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";

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