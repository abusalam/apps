<?php

class Message {

  protected $User;
  protected $MsgID;
  protected $Msg;

  public function setUser($User) {
    $this->User = $User;
  }

  public function getUser() {
    return $this->User;
  }

  public function setMsg($Msg) {
    $this->Msg = $Msg;
  }

  public function getMsg() {
    return $this->Msg;
  }

  function createSMS($User, $Message, $GroupName) {
    $Group = new Group();
    $Group->setGroup($GroupName);
    $Gid                   = $Group->getGroupID();
    $this->User            = $User;
    $DB                    = new MySQLiDBHelper();
    $insertData['UserID']  = $User->getMobileNo();
    $insertData['GroupID'] = $Group->getGroupID();
    $insertData['MsgText'] = $Message;
    $MessageID             = $DB->insert(MySQL_Pre . 'SMS_Messages', $insertData);
    $getCon                = new Contact();
    $Cont                  = $getCon->getContactByGroup($Gid);

    foreach ($Cont as $ContactID) {
      $Report    = $this->sendSMS($Message, $ContactID['MobileNo']);
      $pos1      = strpos($Report, "=");
      $S1        = substr($Report, $pos1 + 1);
      $pos2      = strpos($S1, "~");
      $Status    = substr($S1, 0, $pos2);
      $this->Msg = $Message;
      $Mid       = $this->MsgID = $MessageID;
      $this->CreateStatus($Mid, $Report, $ContactID['MobileNo'], $Status);
    }
    $this->Msg = $Message;
    $Mid       = $this->MsgID = $MessageID;

    return $Mid;
  }

  function sendSMS($Message, $MobileNo) {
    $Message .= "\n--\n" . $this->User->getDesignation();
    $Message .= "\n" . date('l d/m/Y H:i:s', time());
    //$Message .= "\nPlayStore: http://goo.gl/hwAWuA";
    $Status = SMSGW::SendSMS($Message, $MobileNo);

    return $Status;
  }

  function getAllSMS() {
    $DB  = new MySQLiDBHelper();
    $SMS = $DB->get(MySQL_Pre . 'SMS_Messages');

    return $SMS;
  }

  function getSMSByUser() {
    $DB  = new MySQLiDBHelper();
    $Uid = $this->getUser();
    $DB->where('UserID', $Uid);
    $SMS = $DB->get(MySQL_Pre . 'SMS_Messages');
    print_r($SMS);

    return $SMS;
  }

  function CreateStatus($MessageID, $Report, $MobileNo, $Status) {
    $DB                      = new MySQLiDBHelper();
    $insertData['MessageID'] = $MessageID;
    $insertData['Report']    = $Report;
    $insertData['MobileNo']  = $MobileNo;
    $insertData['Status']    = $Status;
    $StatusID                = $DB->insert(MySQL_Pre . 'SMS_Status', $insertData);
  }
}

?>
