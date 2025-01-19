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

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    LinkComponent::get_stylesheets(),
    [$base_url . "/pages/home/styles.css"],
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main id="site-main">
        <div id="container">
            <h2 id="welcome-heading">Добре дошли в Alumni Hub</h2>
            <div id="welcome-banner">
                <img src="static/images/alumni-hats.png" alt="alumni hats">
            </div>
            <p id="welcome-info">
                Чрез този портал можете да получите актуална информация за предстоящото дипломиране и да организирате
                събития за него. Информация за събитията можете да получите само след регистрация. Този портал е
                достъпен
                само за завършили студенти и администрацията.
            </p>

            <nav id="welcome-links">
                <?php
                $link = new LinkComponent("ВЛИЗАНЕ", "$base_url/login");
                echo $link->render();
                $link = new LinkComponent("РЕГИСТРИРАНЕ", "$base_url/register");
                echo $link->render();
                ?>
            </nav>
        </div>
    </main>

    <?php echo $footer->render(); ?>
</body>

</html>