<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Menu Management');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeCSS('users/css/MenuACL.css');
?>
</head>
<body>
<div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
</div>
<div class="Header">
</div>
<?php
include __DIR__ . '/MenuData.php';
WebLib::ShowMenuBar('USER');
?>
<div class="content">
    <h2>Manage Menu ACLs</h2>
    <hr/>
  <?php
  WebLib::ShowMsg();
  $Data         = new MySQLiDBHelper();
  $NoMenuStatus = true;

  if ((WebLib::GetVal($_POST, 'CmdMenuAction') === 'Show Restricted Users') && isset($_POST['MenuID'])) {
    $Query    = 'Select `U`.`UserMapID`,CONCAT(`UserName`,\' [\',`U`.`UserMapID`,\']\') as `UserName` '
      . ' FROM `' . MySQL_Pre . 'Users` as `U` JOIN `' . MySQL_Pre . 'MenuACL` as `A` '
      . ' ON (`A`.`UserMapID`=`U`.`UserMapID`) Where `A`.`MenuID`=?  Order By `UserName`';
    $RowsUser = $Data->rawQuery($Query, array('MenuID' => $_POST['MenuID'][0]));
    $Query    = 'Select `M`.`MenuID`,`AppID`,'
      . ' CONCAT(`Caption`,\' [\',`UserMapID`,\'-\',`A`.`Activated`,\']\') as `Caption`'
      . ' FROM `' . MySQL_Pre . 'MenuItems` as `M` JOIN `' . MySQL_Pre . 'MenuACL` as `A` '
      . ' ON (`A`.`MenuID`=`M`.`MenuID`) Where `A`.`MenuID`=? Order By `AppID`,`MenuOrder`';
    $RowsMenu = $Data->rawQuery($Query, array('MenuID' => $_POST['MenuID'][0]));
  } else {
    if ((WebLib::GetVal($_POST, 'CmdMenuAction') === 'Show Restricted Menus') && isset($_POST['UserMapID'])) {

      $Query    = 'Select `M`.`MenuID`,`AppID`,'
        . ' CONCAT(`Caption`,\' [\',`UserMapID`,\'-\',`A`.`Activated`,\']\') as `Caption`'
        . ' FROM `' . MySQL_Pre . 'MenuItems` as `M` JOIN `' . MySQL_Pre . 'MenuACL` as `A` '
        . ' ON (`A`.`MenuID`=`M`.`MenuID`) Where `UserMapID` in (?) Order By `AppID`,`MenuOrder`';
      $RowsMenu = $Data->rawQuery($Query, array('UserMapID' => implode(',', $_POST['UserMapID'])));

      $Query    = 'Select `UserMapID`,CONCAT(`UserName`,\' [\',`UserMapID`,\']\') as `UserName` '
        . ' FROM `' . MySQL_Pre . 'Users` as `U` Where `UserMapID` in (?)  Order By `UserName`';
      $RowsUser = $Data->rawQuery($Query, array('UserMapID' => implode(',', $_POST['UserMapID'])));
    } else {
      $RowsUser     = $Data->rawQuery('Select `UserMapID`,`UserName` '
        . ' FROM `' . MySQL_Pre . 'Users`  Order By `UserName`');
      $RowsMenu     = $Data->rawQuery('Select `MenuID`,`AppID`,`Caption` '
        . ' FROM `' . MySQL_Pre . 'MenuItems` Order By `AppID`,`MenuOrder`');
      $NoMenuStatus = true;
    }
  }
  ?>
    <form method="post" action="MenuACL.php">
        <div class="column">
            <ul>
              <?php
              echo '<li class="ListItem">Total Users: ' . (count($RowsUser) - 1) . '</li>';
              foreach ($RowsUser as $Index => $User) {
                if ($User['UserMapID'] > 1) {
                  echo '<li class="ListItem">'
                    . '<label for="User' . $User['UserMapID'] . '" >'
                    . '<input id="User' . $User['UserMapID'] . '" type="checkbox" name="UserMapID[]" '
                    . 'value="' . $User['UserMapID'] . '" />'
                    . htmlentities($User['UserName'])
                    . '</label></li>';
                }
              }
              ?>
            </ul>
        </div>
        <div class="column">
            <ul>
              <?php
              $Status[0] = " Allowed";
              $Status[1] = " Restricted";

              echo '<li class="ListItem">Total Menus: ' . count($RowsMenu) . '</li>';
              foreach ($RowsMenu as $Index => $Menu) {
                if ($NoMenuStatus) {
                  $MenuStatus = '';
                } else {
                  $MenuStatus = $Status[substr($Menu['Caption'], -2, 1)];
                }
                echo '<li class="ListItem">'
                  . '<label for="Menu' . $Menu['MenuID'] . '" >'
                  . '<input id="Menu' . $Menu['MenuID'] . '" type="checkbox" name="MenuID[]" '
                  . 'value="' . $Menu['MenuID'] . '" />'
                  . '<strong>' . $Menu['AppID'] . '=>' . $Menu['Caption'] . $MenuStatus . '</strong>'
                  . '</label></li>';
              }
              ?>
            </ul>
        </div>
        <div style="clear: both;"></div>
        <input type="submit" name="CmdMenuAction" value="Refresh"/>
        <input type="submit" name="CmdMenuAction" value="Restrict Menu"/>
        <!-- input type="submit"  name="CmdMenuAction" value="Delete Menu ACL" / -->
        <input type="submit" name="CmdMenuAction"
               value="Activate Menu Restriction"/>
        <input type="submit" name="CmdMenuAction"
               value="Deactivate Menu Restriction"/>
        <input type="submit" name="CmdMenuAction"
               value="Show Restricted Menus"/>
        <input type="submit" name="CmdMenuAction"
               value="Show Restricted Users"/>
        <input type="hidden" name="FormToken"
               value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>"/>
    </form>

  <?php
  unset($Data);
  unset($RowsUser);
  unset($RowsMenu);
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

