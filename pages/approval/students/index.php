<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/table/table_component.php";
require_once __DIR__ . "/../../../models/user.php";

function approve($controller, $username, $student_fn)
{
    $base_url = $_ENV["BASE_URL"];
    $controller->approve_student($username, $student_fn);
    header("Location: $base_url/admin/approval/students");
}

if (isset($_GET["approval"])) {
    $arguments = explode(',', $_GET["approval"], 2);
    approve($controller, $arguments[0], $arguments[1]);
}

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();
$table = new TableComponent(
    ["Потребителско име", "Имейл", "Име", "Факултетен номер"],
    $controller->get_requests_data(),
    "Одобри",
    function ($values) {
        return ["approval" => $values["username"] . "," . $values["fn"]]; }
);

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    $table->get_stylesheets(),
    ["$base_url/pages/approval/administrators/styles.css"]
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main id="site-main">
        <section id="approval-section">
            <h3>Списък със студенти</h3>
            <?php
            echo $table->render();
            ?>
        </section>
    </main>

    <?php echo $footer->render(); ?>
</body>

</html>