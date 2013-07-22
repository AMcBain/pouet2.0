<?
include_once("bootstrap.inc.php");
include_once("include_pouet/box-modalmessage.php");
include_once("include_pouet/box-party-submit.php");

if ($currentUser && !$currentUser->CanSubmitItems())
{
  redirect("index.php");
  exit();
}

$TITLE = "submit a party";

$box = new PouetBoxSubmitParty();
$errors = array();
if ($_POST && $currentUser && $currentUser->CanSubmitItems())
{
  $errors = $box->ParsePostMessage( $_POST );
  if (!count($errors))
  {
    redirect("party.php?which=".(int)$partyID);
  }
}

include("include_pouet/header.php");
include("include_pouet/menu.inc.php");

echo "<div id='content'>\n";

if ($currentUser)
{
  if (count($errors))
  {
    $msg = new PouetBoxModalMessage( true );
    $msg->classes[] = "errorbox";
    $msg->title = "An error has occured:";
    $msg->message = "<ul><li>".implode("</li><li>",$errors)."</li></ul>";
    $msg->Render();
  }

  $box->Load();
  printf("<form action='%s' method='post' enctype='multipart/form-data'>\n",_html(selfPath()));
  $box->Render();
  printf("</form>");

?>
<script type="text/javascript">
document.observe("dom:loaded",function(){
  NameWarning({"ajaxURL":"./ajax_parties.php","linkURL":"party.php?which="});  
});
</script>
<?

}
else
{
  include_once("include_pouet/box-login.php");
  $box = new PouetBoxLogin();
  $box->Render();
}

echo "</div>\n";

include("include_pouet/menu.inc.php");
include("include_pouet/footer.php");

?>