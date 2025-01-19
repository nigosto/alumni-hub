<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../components/button/link.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();
$link = new LinkComponent("Към началния екран", "$base_url/");

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    $link->get_stylesheets(),
    ["$base_url/pages/access_denied/styles.css"]
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main id="site-main">
        <section id="access-denied-section">
            <h3>Отказан достъп</h3>
            <p>Вие нямате достъп до тази страница</p>
            <?php echo $link->render(); ?>
        </section>
    </main>

    <?php echo $footer->render(); ?>
</body>

</html>