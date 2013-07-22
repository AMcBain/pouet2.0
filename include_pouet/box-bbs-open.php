<?
class PouetBoxBBSOpen extends PouetBox {
  var $topic;
  function PouetBoxBBSOpen() {
    parent::__construct();
    $this->uniqueID = "pouetbox_bbsopen";
    $this->title = "open a new bbs thread";
  }

  function ParsePostMessage($post) 
  {
    global $currentUser;  
    if (!$currentUser)
      return "you have to be logged in!";

    if (!$currentUser->CanPostInBBS())
      return "not allowed lol.";
    
    $message = trim($post["message"]);
    if (!$message)
      return "not too meaningful, is it...";

    $title = trim($post["topic"]);
    if (strlen($title) < 2)
      return "not too meaningful, is it...";

    $r = SQLLib::SelectRow(sprintf_esc("SELECT id FROM bbs_topics where topic='%s'",$title));
    if ($r)
      return "DOUBLEPOST == ROB IS JARIG";

  	$a = array();
  	$a["topic"] = $title;
  	$a["category"] = max(min($post["category"],6),0);
  	$a["userfirstpost"] = $a["userlastpost"] = get_login_id();
  	$a["firstpost"] = $a["lastpost"] = date("Y-m-d H:i:s");

    $id = SQLLib::InsertRow("bbs_topics",$a);

  	$a = array();
  	$a["added"] = date("Y-m-d H:i:s");
  	$a["author"] = get_login_id();
  	$a["post"] = $message;
  	$a["topic"] = $id;

    SQLLib::InsertRow("bbs_posts",$a);

    @unlink("cache/pouetbox_latestbbs.cache");

    return "";

  }

  function RenderBody() 
  {
    global $currentUser;
    if (!get_login_id()) 
    {
      include_once("box-login.php");
      $box = new PouetBoxLogin();
      $box->RenderBody();
    } 
    else 
    {
      if (!$currentUser->CanPostInBBS())
        return;
        
      global $THREAD_CATEGORIES;
      echo "<form action='add.php' method='post'>\n";
      echo "<div class='content'>\n";
      echo " <input type='hidden' name='type' value='bbs'>\n";

      echo " <label for='topic'>topic:</label>\n";
      echo " <input name='topic' id='topic'/>\n";

      echo " <label for='category'>category:</label>\n";
      echo " <select name='category' id='category'>\n";
      foreach($THREAD_CATEGORIES as $k=>$v)
        printf("<option value='%d'>%s</option>",$k,$v);
      echo " </select>\n";

      echo " <label for='message'>message:</label>\n";
      echo " <textarea name='message' id='message'></textarea>\n";
      
      echo " <div><a href='faq.php#BB Code'><b>BB Code</b></a> is allowed here</div>\n";
      echo "</div>\n";
      echo "<div class='foot'>\n";
      echo " <script language='JavaScript' type='text/javascript'>\n";
      echo " <!--\n";
      echo "   document.observe('dom:loaded',function(){ AddPreviewButton($('submit')); });\n";
      echo " //-->\n";
      echo " </script>\n";
      echo " <input type='submit' value='Submit' id='submit'>";
      echo "</div>\n";
      echo "</form>\n";
    }
  }

};

?>