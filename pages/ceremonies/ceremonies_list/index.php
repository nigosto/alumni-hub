<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/table/table_component.php";

$base_url = $_ENV["BASE_URL"];

$header = new HeaderComponent();
$footer = new FooterComponent();
$all_ceremonies_data = $controller->get_ceremonies_data();
$table = new TableComponent(Ceremony::labels(), $all_ceremonies_data);

$stylesheets = array_merge(
  $header->get_stylesheets(),
  $footer->get_stylesheets(),
  $table->get_stylesheets(),
  [$base_url . "/pages/ceremonies/ceremonies_list/styles.css"]
);

$meta = new MetadataComponent($stylesheets, array_merge(
  MessageComponent::get_scripts(),
  ["$base_url/pages/ceremonies/ceremonies_list/script.js"]
));
echo $meta->render();
?>

<body>
  <?php
  echo $header->render();
  ?>
  <main id="site-main">
    <section id="ceremony-section">
      <h3>Списък със церемонии</h3>
      <?php
      echo $table->render();
      ?>
    </section>
  </main>
  <?php
  echo $footer->render();
  ?>
</body>

</html>