<?php
if (!defined('LifeTime')) {
  exit();
}
?>

<h2>Reply Queries:</h2>
<?php
WebLib::ShowMsg();
$Data = new MySQLiDB();
if ($_REQUEST['Reply'] == '1') {
  if (isset($_POST['ReplyTo']) && ($_POST['ReplyTo'] != "") && ($_POST['ReplyTxt'] != "")) {
    if (WebLib::GetVal($_POST, 'FormToken') != null) {
      if (WebLib::GetVal($_POST, 'FormToken') == WebLib::GetVal($_SESSION, 'CSRFToken')) {
        $Saved                 = false;
        $MsgInvalidUser        = '';
        $DB                    = new MySQLiDBHelper();
        $HelpData['ReplyTxt']  = WebLib::GetVal($_POST, 'ReplyTxt', true);
        $HelpData['Replied']   = intval(WebLib::GetVal($_POST, 'ShowFAQ', true));
        $HelpData['ReplyTime'] = date('Y-m-d H:i:s', time());

        $DB->where('HelpID', WebLib::GetVal($_POST, 'ReplyTo', true));
        $HelplineUser = $DB->query('Select UserMapID from ' . MySQL_Pre . 'Helpline');

        if (count($HelplineUser) > 0) {
          $HelplineUser = $HelplineUser[0];
          $DB->where('CtrlMapID', $_SESSION['UserMapID']);
          $DB->where('UserMapID', $HelplineUser['UserMapID']);
          $AllowedUsers = $DB->query('Select UserMapID FROM ' . MySQL_Pre . 'Users');
          if (count($AllowedUsers) > 0) {
            $DB->where('HelpID', WebLib::GetVal($_POST, 'ReplyTo', true));
            $Saved = $DB->update(MySQL_Pre . 'Helpline', $HelpData);
          } else {
            $MsgInvalidUser = 'Invalid Data! ';
          }
        }

        if ($Saved) {
          $_SESSION['Msg'] = "Replied Successfully!";
        } else {
          $_SESSION['Msg'] = $MsgInvalidUser . "Unable to Reply!";
        }
        unset($DB);
      } else {
        $_SESSION['Msg'] = "Request may have been modified!";
      }
    }
  }
  $_SESSION['CSRFToken'] = md5($_SERVER['REMOTE_ADDR'] . session_id() . microtime());
  ?>
    <form name="frmLogin" method="post" action="Helpline.php?Reply=1">
      <?php WebLib::ShowMsg(); ?>
        <label for="ReplyTo">Reply To:</label> <select name="ReplyTo">
        <?php
        $Query = 'SELECT HelpID,CONCAT(\'[\',Replied,\'-\',HelpID,\'] \',UserName) as `AppName` '
          . ' FROM `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
          . ' ON (`H`.UserMapID=`U`.UserMapID) Where CtrlMapID=' . $_SESSION['UserMapID']
          . ' order by Replied,HelpID desc';
        $Data->show_sel('HelpID', 'AppName', $Query, $_POST['ReplyTo']);
        ?>
        </select> <b>Show in FAQ:</b><input type="radio" id="ShowFAQ"
                                            name="ShowFAQ" value="1"/><label
                for="ShowFAQ">Yes</label> <input
                type="radio" id="ShowFAQ" name="ShowFAQ" value="2"/><label
                for="ShowFAQ">No</label><br/> <label
                for="ReplyTxt">Reply:</label><br/>
        <textarea id="ReplyTxt" name="ReplyTxt" rows="12" cols="100"
                  maxlength="300"></textarea><br/>
        <input style="width: 80px;" type="submit" value="Reply"/>
        <input type="hidden" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'CSRFToken') ?>"/>
    </form>
  <?php
  $Query = 'Select * from `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
    . ' ON (`H`.UserMapID=`U`.UserMapID) '
    . ' Where CtrlMapID=' . $_SESSION['UserMapID'] . ' AND Replied!=1 Order by Replied,HelpID DESC';
} else {
  $Query = 'Select * from `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
    . ' ON (`H`.UserMapID=`U`.UserMapID) '
    . ' Where CtrlMapID=' . $_SESSION['UserMapID'] . ' AND Replied<2 Order by HelpID DESC';
}
unset($Data);

$Data = new MySQLiDBHelper();
$FAQs = $Data->query($Query);
unset($Data);

foreach ($FAQs as $row) {
  ?>
    <div class="Notice">
        <b><?php echo '[' . $row['HelpID'] . '] ' . htmlentities($row['UserName']); ?>
            :</b><br/>
      <?php echo str_replace("\r\n", "<br />", htmlentities($row['TxtQry'])); ?>
        <br/>
        <small>
            <i>
              <?php
              echo "From IP: {$row['IP']} On: "
                . date("l d F Y g:i:s A ", strtotime($row['QryTime']));
              ?>
            </i>
        </small>
        <br/><br/>
        <b>Reply[<?php echo $row['Replied']; ?>]:</b>
        <p>
            <i>&ldquo;<?php echo str_replace("\r\n", "<br />", htmlentities($row['ReplyTxt'])); ?>&rdquo;
            </i>
        </p>
        <small>
            <i>
              <?php
              if (WebLib::GetVal($row, 'ReplyTime') != null) {
                echo "[" . $row['UserID'] . "] Replied On: "
                  . date("l d F Y g:i:s A", strtotime($row['ReplyTime']));
              } else {
                echo "[" . $row['UserID'] . "] Pending for Reply";
              }
              ?>
            </i>
        </small>
    </div>
  <?php
}
?>

