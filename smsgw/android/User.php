<?php

class User {

  protected $MobileNo;
  protected $UserName;
  protected $Designation;
  protected $eMailID;
  protected $UserMapID;

  function __construct($MobileNo) {
    $DB = new MySQLiDBHelper();
    $DB->where('MobileNo', $MobileNo);
    $Users             = $DB->get(MySQL_Pre . 'APP_Users');
    $this->eMailID     = $Users[0]['eMailID'];
    $this->UserName    = $Users[0]['UserName'];
    $this->UserMapID   = $Users[0]['UserMapID'];
    $this->Designation = $Users[0]['Designation'];
    $this->MobileNo    = $MobileNo;
  }

  /**
   * @return mixed
   */
  public function getDesignation() {
    if ($this->Designation == "") {
      return $this->getMobileNo();
    } else {
      return $this->Designation;
    }
  }

  function getMobileNo() {
    return $this->MobileNo;
  }

  /**
   * @return mixed
   */
  public function getUserName() {
    return $this->UserName;
  }

  /**
   * @return mixed
   */
  public function getEMailID() {
    return $this->eMailID;
  }

  /**
   * @return mixed
   */
  public function getUserMapID() {
    return $this->UserMapID;
  }

  function isAuthUser() {
    return true;
  }

  /**
   * @param $UserName
   * @param $Password
   * @return int
   */
  function createUser($UserName, $Password) {
    $DB                     = new MySQLiDBHelper();
    $Pass                   = md5($Password);
    $insertData['UserName'] = $UserName;
    $insertData['Password'] = $Pass;
    $insertData['Status']   = 'off';
    $UserID                 = $DB->insert(MySQL_Pre . 'APP_Users', $insertData);

    return $UserID;
  }
}
