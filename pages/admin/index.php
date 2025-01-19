<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../components/table/table_component.php";
require_once __DIR__ . "/../../models/user.php";

function approve($controller, $email)
{
    $base_url = $_ENV["BASE_URL"];
    $controller->approve_administrator($email);
    header("Location: $base_url/admin/approval");
}

if (isset($_GET["approve"])) {
    approve($controller, $_GET["approve"]);
}

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();
$table = new TableComponent(
    User::labels(),
    $controller->get_users_data_by_role(Role::Administrator),
    "Одобри",
    function ($values) {
        return ["approve" => $values[0]]; },
    function ($values) {
        return $values[3] === "Да"; }
);

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    $table->get_stylesheets(),
    ["$base_url/pages/admin/styles.css"]
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main id="site-main">
        <section id="approval-section">
            <h3>Списък с потребители</h3>
            <?php
            echo $table->render();
            ?>
        </section>
    </main>

    <?php echo $footer->render(); ?>
</body>

</html>