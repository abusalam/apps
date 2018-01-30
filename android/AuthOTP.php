<?php
require_once(__DIR__ . '/../lib.inc.php');
require_once(__DIR__ . '/ga4php.php');

class AuthOTP extends GoogleAuthenticator {

  const TOKEN_DATA_TEMP = 1;
  const TOKEN_DATA_USER = 0;
  private $Mode;
  private $UserMapID;

  function __construct($Mode = self::TOKEN_DATA_USER) {
    /**
     * Important: Key Skew and Hunt values needs to be set.
     */
    parent::__construct();
    $this->Mode = $Mode;
  }

  /**
   * "select tokendata from users where username='$username'"
   *
   * @param $UserID
   *
   * @returns string $TokenData
   */
  function getData($UserID) {
    // TODO: Implement getData() method.
    $MySQLiDB = new MySQLiDBHelper();
    $User     = $MySQLiDB->where('MobileNo', $UserID)
      ->get(MySQL_Pre . 'APP_Users');
    if (count($User) > 0) {
      $this->UserMapID = $User[0]['UserMapID'];
      if ($this->Mode == self::TOKEN_DATA_USER) {
        return $User[0]['UserData'];
      } else {
        return $User[0]['TempData'];
      }
    }
  }

  /**
   * @return mixed
   */
  public function getUserMapID() {
    return $this->UserMapID;
  }

  /**
   * "update users set tokendata='$data' where username='$username'"
   *
   * This function returns true if updated otherwise false
   *
   * @param $UserID
   * @param $TokenData
   *
   * @return boolean
   */
  function putData($UserID, $TokenData) {
    // TODO: Implement putData() method.
    $MySQLiDB = new MySQLiDBHelper();
    if ($this->Mode == self::TOKEN_DATA_USER) {
      $Data['UserData'] = $TokenData;
    } else {
      $Data['TempData'] = $TokenData;
    }
    if ($MySQLiDB->where('MobileNo', $UserID)
        ->update(MySQL_Pre . 'APP_Users', $Data) == 0) {
      $MySQLiDB->insert(MySQL_Pre . 'APP_Users', $Data);
    }
    $_SESSION['TokenOTP'] = $TokenData;

    return true;
  }

  /**
   * Return all registered and activated users
   * i.e. an Array of user names only
   */
  function getUsers() {
    // TODO: Implement getUsers() method.
    $MySQLiDB = new MySQLiDBHelper();
    $UserIDs  = $MySQLiDB->query('Select MobileNo from ' . MySQL_Pre . 'APP_Users');

    return $UserIDs;
  }

} 