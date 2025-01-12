<!DOCTYPE html>
<html lang="bg">

<?php
require __DIR__ . "/../../components/meta/index.php";
require __DIR__ . "/../../components/header/index.php";
require __DIR__ . "/../../components/footer/index.php";

$header = new HeaderComponent();
$footer = new FooterComponent();

$base_url = $_ENV["BASE_URL"];

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/register/styles.css"],
    [$base_url . "/components/styles/input.css"],
    [$base_url . "/components/styles/form.css"],
    [$base_url . "/components/styles/button.css"],

);

$meta = new MetadataComponent($stylesheets, ["../pages/register/script.js"]);
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    ?>

    <main class="container">
        <h1>Регистрация в Alumni Hub</h1>

        <form id="registrationForm">
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

            <button type="submit" class="button">Регистрация</button>
        </form>

        <nav class="form">
            <a href="#" id="loginBtn" class="button">Вече имаш акаунт</a>
            <a href="<?php echo $base_url . "/home" ?>" id="loginBtn" class="button">Обратно в началния екран</a>
        </nav>

    </main>

    <?php echo $footer->render();
    ?>
</body>

</html>