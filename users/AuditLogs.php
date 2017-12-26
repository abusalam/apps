<?php
require_once __DIR__ . '/../lib.inc.php';
WebLib::AuthSession();
WebLib::Html5Header("User Activity");
WebLib::IncludeCSS();
WebLib::IncludeCSS('mpr/css/forms.css');
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
WebLib::ShowMenuBar('USER');
?>
<div class="content">
  <h2>Activity Logs</h2>
  <hr/>
  <div class="formWrapper">
    <form action="" method="post">
      <div class="FieldGroup">
        <label for="UserMapID"><strong>User:</strong></label><br/>
        <select id="UserMapID" name="UserMapID">
          <option></option>
          <?php
          $UserMapID = WebLib::GetVal($_POST, 'UserMapID');
          $DB = new MySQLiDBHelper();
          $DB->where('CtrlMapID', $_SESSION['UserMapID']);
          $Users = $DB->get(MySQL_Pre . 'Users');
          if($UserMapID==$_SESSION['UserMapID']) {
            $Selected='selected="selected"';
          } else {
            $Selected='';
          }
          echo '<option value="' . $_SESSION['UserMapID'] . '" '. $Selected .'>'
            . $_SESSION['UserMapID'] . ' - ' . htmlentities($_SESSION['UserName']) . '</option>';
          foreach ($Users as $User) {
            if($UserMapID==$User['UserMapID']) {
              $Selected='selected="selected"';
            } else {
              $Selected='';
            }
            echo '<option value="' . $User['UserMapID'] . '" '. $Selected .'>'
              . $User['UserMapID'] . ' - ' . htmlentities($User['UserName']) . '</option>';
          } ?>
        </select>
        <input type="Submit" value="Show Activity" name="BtnShow">
      </div>
      <div style="clear: both;"></div>
      <hr/>
    </form>
    <div id="DataTable">
      <?php
      //$DB->where('UserID',WebLib::GetVal($_POST,'User'));
      $DB = new mysqli(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);

      if ($_SESSION['UserMapID'] == $UserMapID || is_null($UserMapID)) {
        $LogQuery = 'Select * from ' . MySQL_Pre . 'Logs Where UserMapID='
          . $_SESSION['UserMapID'] . ' Order By LogID Desc limit 50';
      } else {
        $LogQuery = 'Select L.* from ' . MySQL_Pre . 'Logs L inner join '
          . MySQL_Pre . 'Users U' . ' on L.UserMapID=U.UserMapID Where L.UserMapID='
          . $UserMapID . ' AND U.CtrlMapID=' . $_SESSION['UserMapID']
          . ' Order By LogID Desc limit 50';
      }

      $Results = $DB->query($LogQuery);
      $Logs = array();

      if ($Results) {
        while ($Log = mysqli_fetch_array($Results, MYSQLI_ASSOC)) {
          array_push($Logs, $Log);
        }
      }

      //echo $LogQuery;
      WebLib::ShowTable($Logs);

      unset($DB);
      ?>
    </div>
  </div>
</div>
<div class="pageinfo">
  <?php WebLib::PageInfo(); ?>
</div>
<div class="footer">
  <?php WebLib::FooterInfo(); ?>
</div>
</body>
</html>
