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
$all_ceremonies_data = $controller->get_ceremonies_list_data();
$all_ceremonies_rest = [];
$all_ceremonies_ids = [];

foreach ($all_ceremonies_data as $ceremony_data) {
    $all_ceremonies_rest[] = array_diff_key($ceremony_data, array_flip(['id']));;
    $all_ceremonies_ids[] = $ceremony_data['id'];
}

$ceremonies_cur_idx = 0;

$table = new TableComponent(
  Ceremony::labels(), 
  $all_ceremonies_rest,
  "Инфо",
  function ($values) use ($all_ceremonies_ids, &$ceremonies_cur_idx) {
      $ceremony_id = $all_ceremonies_ids[$ceremonies_cur_idx++];
      return "ceremony/students/$ceremony_id"; });

$stylesheets = array_merge(
  $header->get_stylesheets(),
  $footer->get_stylesheets(),
  $table->get_stylesheets(),
  [$base_url . "/pages/ceremonies/ceremonies_list/styles.css"]
);

$meta = new MetadataComponent($stylesheets);
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