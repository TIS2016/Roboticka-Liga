<?php
header('Content-type: text/plain; charset=utf-8');
ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));
session_start();
require_once(dirname(__FILE__)."/functions.php");
if (isset($_GET["year"])) $yr = $_GET["year"];
else $yr = NULL;
$sk_table = get_result_table(1, $yr);
$open_table = get_result_table(0, $yr);
if ($sk_table == "" && $open_table == ""){
    echo "<p class='center' data-trans-key='results-not-available'></p>";
}
else {
    echo $sk_table;
    echo $open_table;
}
?>
    <script>
        dict.translateElement(null, "#results");
    </script>
<?php
?>
