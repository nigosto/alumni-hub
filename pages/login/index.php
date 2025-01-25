<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../components/button/link.php";
require_once __DIR__ . "/../../components/message/message_component.php";

$header = new HeaderComponent();
$footer = new FooterComponent();
$message = new MessageComponent(MessageVariant::Error);
$info_message = new MessageComponent(MessageVariant::Success);

$base_url = $_ENV["BASE_URL"];

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/login/styles.css"],
    [$base_url . "/components/styles/input.css"],
    [$base_url . "/components/styles/form.css"],
    ButtonComponent::get_stylesheets(),
    MessageComponent::get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, array_merge(
    MessageComponent::get_scripts(),
    ["$base_url/pages/login/script.js"]
));
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    ?>

    <main class="container">
        <h1>Вход в профила Ви в Alumni Hub</h1>

        <form id="login-form">
            <input type="text" id="username" name="username" placeholder="Потребителско име" required>
            <input type="password" id="password" name="password" placeholder="Парола" required>

            <?php
            $submit_button = new ButtonComponent("Вход", ButtonStyleType::Primary, true);
            echo $submit_button->render();
            ?>
        </form>

        <nav class="nav-buttons">
            <?php
            $link = new LinkComponent("Регистрация", "$base_url/register");
            echo $link->render();
            $link = new LinkComponent("Към началния екран", "$base_url/");
            echo $link->render();
            ?>
        </nav>
        <?php echo $message->render(); ?>
        <?php echo $info_message->render(); ?>

    </main>

    <?php echo $footer->render();
    ?>
</body>

</html>