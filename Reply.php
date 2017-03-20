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
    $DB                    = new MySQLiDBHelper();
    $HelpData['ReplyTxt']  = WebLib::GetVal($_POST, 'ReplyTxt', true);
    $HelpData['Replied']   = intval(WebLib::GetVal($_POST, 'ShowFAQ', true));
    $HelpData['ReplyTime'] = time();
    $DB->where('HelpID', WebLib::GetVal($_POST, 'ReplyTo', true));
    $DB->update(MySQL_Pre . 'Helpline', $HelpData);
    unset($DB);
  }
  ?>
    <form name="frmLogin" method="post"
          action="<?php $_SERVER['PHP_SELF'] ?>">
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
    </form>
  <?php
  $Data->do_sel_query('Select * from `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
    . ' ON (`H`.UserMapID=`U`.UserMapID) '
    . ' Where CtrlMapID=' . $_SESSION['UserMapID'] . ' AND Replied!=1 Order by Replied,HelpID DESC');
} else {
  $Data->do_sel_query('Select * from `' . MySQL_Pre . 'Helpline` `H` JOIN `' . MySQL_Pre . 'Users` `U`'
    . ' ON (`H`.UserMapID=`U`.UserMapID) '
    . ' Where CtrlMapID=' . $_SESSION['UserMapID'] . ' AND Replied<2 Order by HelpID DESC');
}

while ($row = $Data->get_row()) {
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
              echo "[" . $row['UserID'] . "] Replied On: "
                . date("l d F Y g:i:s A", strtotime($row['ReplyTime']));
              ?>
            </i>
        </small>
    </div>
  <?php
}
?>

