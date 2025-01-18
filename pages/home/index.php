<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/home/styles.css"],
    [$base_url . "/components/styles/button.css"],
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    ?>

    <main>
        <header>
            <h1>Добре дошъл в Alumni Hub</h1>
        </header>
        <p>
            Чрез този портал можеш да получиш актуална информация за предстоящото дипломиране и да организираш
            събития за него. Информация за събитията можеш да получиш само след регистрация. Този портал е достъпен
            само за завършили студенти и администрацията.
        </p>

        <nav>
            <a href="#" id="loginBtn" class="button">Влез в профила си</a>
            <a href="<?php echo $base_url . "/register" ?>" id="registerBtn" class="button">Създай нов профил</a>
        </nav>
    </main>

    <?php echo $footer->render();
    ?>
</body>

</html>