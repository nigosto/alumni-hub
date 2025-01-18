<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require __DIR__ . "/../../components/button/link.php";

$header = new HeaderComponent();
$footer = new FooterComponent();

$base_url = $_ENV["BASE_URL"];

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/register/styles.css"],
    [$base_url . "/components/styles/input.css"],
    [$base_url . "/components/styles/form.css"],
    ButtonComponent::get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, ["$base_url/pages/register/script.js"]);
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    ?>

    <main class="container">
        <h1>Регистрация в Alumni Hub</h1>

        <form id="registration-form">
            <select id="account-type" name="account-type">
                <option value="">Тип на акаунта</option>
                <option value="student" id="student-account">Студентски акаунт</option>
                <option value="administrator" id="administration-account">Адиминистраторски акаунт
                </option>
            </select>

            <input type="text" id="username" name="username" placeholder="Потребителско име" required>
            <input type="email" id="email" name="email" placeholder="Имейл адрес" required>
            <input type="password" id="password" name="password" placeholder="Парола" required>
            <input type="password" id="password-confirmation" name="password-confirmation"
                placeholder="Потвърждение на паролата" required>
            <input type="text" id="fn" name="fn" placeholder="Факултетен номер" class="hidden">

            <?php
                $submit_button = new ButtonComponent("Регистрация", ButtonStyleType::Primary, true);
                echo $submit_button->render();
            ?>
        </form>

        <nav class="form">
            <?php
                $link = new LinkComponent("Вече имате акаунт", "#");
                echo $link->render();
                $link = new LinkComponent("Към началния екран", "$base_url/");
                echo $link->render();
            ?>
        </nav>

    </main>

    <?php echo $footer->render();
    ?>
</body>

</html>