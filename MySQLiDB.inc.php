<?php

/*
 * Class DB helper class for MySQL database handling functions
 *
 * @version v1.0
 */
if (file_exists(__DIR__ . '/config.inc.php')) {
  require_once __DIR__ . '/config.inc.php';
} else {
  require_once __DIR__ . '/config.sample.inc.php';
}

/**
 * Class DB helper class for MySQL database handling functions
 * using mysqli php extension
 *
 * @todo Make use of MySQLi Extension instead of MySQL Extension
 */
class MySQLiDB {

  /**
   * Database connection resource
   * @var resource
   * @access private
   */
  private $conn;

  /**
   * Result resourse
   *
   * @var resource
   * @access private
   */
  private $RecSet;

  /**
   * Set debuging On if set to 1.
   *
   * @var int
   */
  private $Debug;

  /**
   * Number of rows returned by the executed query.
   *
   * @var int $RowCount
   * @access private
   */
  private $RowCount;

  /**
   * Number of fields returned by the executed query.
   *
   * @var int
   * @access private
   */
  private $ColCount;

  /**
   * Set to 1 if no result returned by query.
   *
   * @var boolean
   * @access private
   */
  private $NoResult;

  private $DB;

  function __construct() {
    $this->NoResult = 1;
    $this->DB=new MySQLiDBHelper();
    //trigger_error("This Module is Deprecated use MySQLi instead",E_USER_DEPRECATED);
  }

  function __get($var) {
    switch ($var) {
      case 'RowCount' :
        return $this->RowCount;
        break;
      case 'ColCount' :
        return $this->ColCount;
        break;
      case 'NoResult' :
        return $this->NoResult;
        break;
    }
  }

  function __set($var, $val) {
    switch ($var) {
      case 'Debug' :
        $this->Debug = $val;
        break;
    }
  }

  public function __sleep() {
    $this->do_close();
    return array('conn', 'result', 'Debug');
  }

  public function __wakeup() {
    $this->do_connect();
  }

  /**
   * Initiates connection to the database
   */
  private function do_connect() {
    //$this->Debug=1;
    $this->conn = mysql_connect(HOST_Name, MySQL_User, MySQL_Pass);
    if (!$this->conn) {
      die('Could not Connect: ' . mysql_error() . "<br><br>");
    }
    mysql_select_db(MySQL_DB) or die('Cannot select database: ' . mysql_error() . "<br><br>");
    $this->NoResult = 1;
  }

  /**
   * Converts/escapes a string to mysql safe string
   *
   * @param string $StrValue
   * @return string
   */
  public function SqlSafe($StrValue) {
    $this->do_connect();
    return mysql_real_escape_string($StrValue);
  }

  /**
   * Executes a select query and returns the first field value of the top row
   *
   * @param string $Query
   * @return int|string Returns 0 if Max is null otherwise Max Value
   */
  public function do_max_query($Query) {
    $Record = $this->DB->query($Query);
    $RowData = array_values($Record[0]);
    //echo "Whole Row: ".$row[0].$row[1];
    if ($RowData[0] == null)
      return 0;
    else
      return htmlentities($RowData[0]);
  }

  /**
   * Executes a insert/update query
   *
   * Returns the number of rows affected
   *
   * @param string $querystr
   * @return int
   */
  public function do_ins_query($TableName, $FieldData=array()) {
    $Data = new MySQLiDBHelper();
    $this->RecSet = $Data->insert($TableName, $FieldData);
    unset($Data);
    if (!$this->RecSet) {
      $message = 'Error in Inserting into database.'; // . mysql_error();
      //$message .= 'Whole query: '. $querystr."<br>";
      if ($this->Debug)
        $_SESSION['Msg'] = $message;
      trigger_error($message, E_USER_NOTICE);
      $this->RowCount = 0;
      return 0;
    }
    $this->NoResult = 1;
    return $this->RecSet;
  }

  /**
   * Executes a select query
   *
   * Returns the number of rows fetched
   *
   * @param string $querystr
   * @return integer
   */
  public function do_sel_query($querystr) {
    $this->do_connect();
    $this->RecSet = mysql_query($querystr, $this->conn);
    if (mysql_errno($this->conn)) {
      if ($this->Debug)
        $_SESSION['Msg'] = mysql_error($this->conn);
      $this->NoResult = 1;
      $this->RowCount = 0;
      return 0;
    }
    $this->NoResult = 0;
    $this->RowCount = mysql_num_rows($this->RecSet);
    $this->ColCount = mysql_num_fields($this->RecSet);
    return $this->RowCount;
  }

  /**
   * Returns the top associative row from the result
   * that is fetched by previous select query
   *
   * @return array
   */
  public function get_row() {
    if (!$this->NoResult)
      return mysql_fetch_assoc($this->RecSet);
  }

  /**
   * Returns the top numeric indexed row from the result
   * that is fetched by previous select query
   *
   * @return type
   */
  public function get_n_row() {
    if (!$this->NoResult)
      return mysql_fetch_row($this->RecSet);
  }

  /**
   * Returns the FieldName of specified index from the result
   * that is fetched by previous select query
   *
   * @param int $ColPos
   * @return string
   */
  public function GetFieldName($ColPos) {
    if (mysql_errno())
      return "ERROR!";
    else if ($this->ColCount > $ColPos)
      return mysql_field_name($this->RecSet, $ColPos);
    else
      return "Offset Error!";
  }

  /**
   * Returns the TableName of specified index from the result
   * that is fetched by previous select query
   *
   * @param int $ColPos
   * @return string
   */
  public function GetTableName($ColPos) {
    if (mysql_errno())
      return "ERROR!";
    else if ($this->ColCount > $ColPos)
      return mysql_field_table($this->RecSet, $ColPos);
    else
      return "Offset Error!";
  }

  /**
   * Returns the descriptive caption of the field from a table
   *
   * @param string $ColName Name of the database field
   * @return string
   */
  function GetCaption($ColName, $FieldsTable = "SRER_FieldNames") {
    $Fields = new MySQLiDB();
    $ColHead = $Fields->do_max_query("Select `Description` from `" . MySQL_Pre . "{$FieldsTable}`"
            . " Where `FieldName`='{$ColName}'");
    $Fields->do_close();
    unset($Fields);
    return (!$ColHead ? $ColName : $ColHead);
  }

  /**
   * Displays a HTML Combo filled with options specified by $txt & $val
   *
   * @param string $val Name of the Field which will be used as value
   * @param string $txt Name of the Field which will be shown in options
   * @param string $query Should select the $val & $txt fields
   * @param string $sel_val Value of the Option to be selected
   * @example Output: <option value="$row[$val]"> $row[$txt] < /option>;
   * htmlspecialchars() applied to all the values
   */
  public function show_sel($val, $txt, $query, $sel_val = "") {
    $Rows=$this->DB->rawQuery($query);
    echo "<option value=''></option>";
    foreach ($Rows as $Row) {
      if ($Row[$val] == $sel_val)
        $sel = "selected";
      else
        $sel = "";
      echo '<option value="' . htmlentities($Row[$val])
      . '"' . $sel . '>' . htmlentities($Row[$txt]) . '</option>';
    }
  }

  /**
   * Displays the data in a table
   *
   * @param String $QueryString
   * @return int Number of Total Rows displayed
   */
  public function ShowTable($QueryString) {
    // Performing SQL query
    $Rows=$this->DB->rawQuery($QueryString);
    // Printing results in HTML
    echo '<table rules="all" frame="box" width="100%" cellpadding="5" cellspacing="1">';
    $Header=true;
    foreach($Rows as $Row){
      if($Header){
        foreach($Row as $Field => $Value){
          echo '<th>' . $Field . '</th>';
        }

      }
      $Header=false;
      echo "\t<tr>\n";
      foreach($Row as $Value){
        echo "\t\t<td>" . $Value . "</td>\n";
      }
      echo "\t</tr>\n";
    }
    echo "</table>\n";
    return (count($Rows));
  }

  /**
   * Closes the current database connection
   */
  public function do_close() {
    // Free resultset
    if (!$this->NoResult) {
      mysql_free_result($this->RecSet);
      $this->NoResult = 1;
    }

    // Closing connection
    if ($this->conn)
      mysql_close($this->conn);
    //echo "<br />LastQuery: ".$LastQuery;
  }

}

?>
