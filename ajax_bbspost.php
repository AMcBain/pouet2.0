<?
include_once("bootstrap.inc.php");

header("Content-type: application/json; charset=utf-8");

$sql = sprintf_esc("select * from bbs_posts where id = %d limit 1",$_POST["id"]);
$r = SQLLib::selectRow($sql);

echo json_encode($r);
?>