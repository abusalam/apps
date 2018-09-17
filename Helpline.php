<?php
//require_once('functions.php');
require_once(__DIR__ . '/lib.inc.php');
WebLib::AuthSession();
WebLib::Html5Header("Helpline");
WebLib::IncludeCSS();
WebLib::JQueryInclude();
?>
<script>
  $(function () {
    $("#HelpLineNotes").accordion({
      heightStyle: "content",
      collapsible: true
    });
    $("#UserNotes").accordion({
      heightStyle: "content",
      collapsible: true
    });
  });

  function limitChars(textarea, limit, infodiv) {
    var text = textarea.value;
    var textlength = text.length;
    var info = document.getElementById(infodiv);
    if (textlength > limit) {
      info.innerHTML = ' (You cannot write more then ' + limit + ' characters!)';
      textarea.value = text.substr(0, limit);
      return false;
    }
    else {
      info.innerHTML = ' (You have ' + (limit - textlength) + ' characters left.)';
      return true;
    }
  }

  function do_submit() {
    var fd_txt = document.feed_frm.feed_txt.value;
    if (fd_txt.length === 0) {
      window.alert("Please write your comment!");
    }
    else
      document.feed_frm.submit();
  }

</script>
</head>
<body>
<div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header"></div>
<?php
WebLib::ShowMenuBar('APPS');
?>
<div class="content">
  <?php
  if (WebLib::GetVal($_GET, 'Reply') !== null) {
    require_once 'Reply.php';
  } else {
    ?>
      <h2>Helpline</h2>
    <?php
    if (WebLib::GetVal($_POST, 'CmdCancel') === 'Cancel') {
      $_SESSION['SendQry'] = "0";
    }
    if ((WebLib::GetVal($_POST, 'SendQry') === "Send Us Your Query") || (WebLib::GetVal($_SESSION, 'SendQry') === '1')) {
      $_SESSION['SendQry'] = '1';
      $fd                  = WebLib::GetVal($_POST, 'feed_txt', true);

      if ((strlen($fd) > 1024) || ($fd === '')) {
        ?>
          <form name="feed_frm" method="post"
                action="Helpline.php" style="text-align: left;">
              <b>Describe your problem: </b><span
                      id="info">(Max: 1024 chars)</span><br/>
              <textarea rows="12" cols="100" style="height: 200px; margin: 0px;"
                        name="feed_txt"
                        onkeyup="limitChars(this, 1024, 'info')"><?php echo $fd; ?></textarea><br/>
              <input name="button" type="button" style="width: 80px;"
                     onclick="do_submit()" value="Send"/>
              <input name="CmdCancel" type="submit" style="width: 80px;"
                     value="Cancel"/>
              <input type="hidden" name="FormToken"
                     value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>"/>
          </form>
        <?php
      } else {
        echo '<h3>Thankyou for your valuable time and appreciation.</h3>'; //.$message;
        $Data = new MySQLiDBHelper();
        if (WebLib::GetVal($_POST, 'FormToken') != null) {
          if (WebLib::GetVal($_POST, 'FormToken') == WebLib::GetVal($_SESSION, 'FormToken')) {
            $HelpData['IP']        = $_SERVER['REMOTE_ADDR'];
            $HelpData['SessionID'] = session_id();
            $HelpData['UserMapID'] = $_SESSION['UserMapID'];
            $HelpData['TxtQry']    = $fd;
            $Submitted             = $Data->insert(MySQL_Pre . 'Helpline', $HelpData);
            if ($Submitted > 0) {
              $_SESSION['SendQry'] = "0";
            } else {
              echo "<h3>Unable to send request.</h3>";
            }
          } else {
            $_SESSION['Msg'] = "Request may have been modified!";
          }
        }
      }
    } else {
      $Data           = new MySQLiDB();
      $QryToRepl      = 'Select count(*) From `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
        . ' ON (`H`.`UserMapID`=`U`.`UserMapID`) '
        . ' Where CtrlMapID=' . $_SESSION['UserMapID'] . ' AND Replied=0';
      $ToBeReplied    = $Data->do_max_query($QryToRepl);
      $QryAsked       = 'Select count(*) From `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
        . ' ON (`H`.`UserMapID`=`U`.`UserMapID`) '
        . ' Where `H`.`UserMapID`=' . $_SESSION['UserMapID'] . ' AND Replied=0';
      $AskedUnReplied = $Data->do_max_query($QryAsked);
      unset($Data);
      $_SESSION['FormToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
      ?>
        <form method="post">
            <div class="FieldGroup">
                <b>Read the Frequently Asked Questions Carefully and then:</b>
                <input name="SendQry" type="submit" value="Send Us Your Query"/><br/>
                <span class="Message">
              <b>Queries to be replied: </b>
              <b> To you: </b><?php echo $AskedUnReplied; ?>,
              <b> By you: </b><a href="?Reply=1"
                                 style="color: #99CC33;"><?php echo $ToBeReplied; ?></a>
            </span>
            </div>
        </form>
        <div style="clear:both;"></div>
        <br/>
        <h2>Frequently Asked Questions:</h2>
      <?php
      $Data = new MySQLiDBHelper();
      $FAQs = $Data->query('Select * from `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
        . ' ON (`H`.UserMapID=`U`.UserMapID) '
        . ' Where Replied=1'
        . ' Order by ReplyTime DESC,HelpID desc');
      if (count($FAQs) > 0) {
        echo '<div id="HelpLineNotes">';
      }
      foreach ($FAQs as $row) {
        ?>
          <h3>
              <b>
                <?php
                echo '[' . WebLib::GetVal($row, 'HelpID') . '] ' . htmlentities($row['UserName'])
                  . " [Replied On: " . date("l d F Y g:i:s A ", strtotime($row['ReplyTime'])) . ']';
                ?>
              </b>
          </h3>
          <div>
            <?php echo str_replace("\r\n", "<br />", htmlentities($row['TxtQry'])); ?>
              <br/>
              <small><i>
                  <?php
                  echo 'From IP: ' . WebLib::GetVal($row, 'IP') . ' On: ' . date("l d F Y g:i:s A ", strtotime($row['QryTime']));
                  ?>
                  </i>
              </small>
              <br/><br/>
              <b>Reply:</b>
              <p>
                  <i>&ldquo;
                    <?php
                    echo str_replace("\r\n", "<br />", htmlentities($row['ReplyTxt']));
                    ?>
                      &rdquo;</i>
              </p>
          </div>
        <?php
      }
      if (count($FAQs) > 0) {
        echo '</div>';
      }
      ?>
        <div style="clear:both;"></div>
      <?php
      $FAQs = $Data->query('Select * from `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
        . ' ON (`H`.UserMapID=`U`.UserMapID) '
        . ' Where Replied=2 and `U`.UserMapID=' . $_SESSION['UserMapID']
        . ' Order by ReplyTime DESC,HelpID desc');
      if (count($FAQs) > 0) {
        echo '<h2>Your Questions:</h2><div id="UserNotes">';
      }
      foreach ($FAQs as $row) {
        ?>
          <h3>
              <b>
                <?php
                echo '[' . WebLib::GetVal($row, 'HelpID') . '] ' . htmlentities($row['UserName'])
                  . " [Replied On: " . date("l d F Y g:i:s A ", strtotime($row['ReplyTime'])) . ']';
                ?>
              </b>
          </h3>
          <div>
            <?php echo str_replace("\r\n", "<br />", htmlentities($row['TxtQry'])); ?>
              <br/>
              <small><i>
                  <?php
                  echo 'From IP: ' . WebLib::GetVal($row, 'IP') . ' On: ' . date("l d F Y g:i:s A ", strtotime($row['QryTime']));
                  ?>
                  </i>
              </small>
              <br/><br/>
              <b>Reply:</b>
              <p>
                  <i>&ldquo;
                    <?php
                    echo str_replace("\r\n", "<br />", htmlentities($row['ReplyTxt']));
                    ?>
                      &rdquo;</i>
              </p>
          </div>
        <?php
      }
      if (count($FAQs) > 0) {
        echo '</div>';
      }
      ?>
        <div style="clear:both;"></div>
      <?php
    }
  }
  ?>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>
