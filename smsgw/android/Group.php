<?php

class Group {

  protected $GroupID;

  static function getAllGroups() {
    $DB     = new MySQLiDBHelper();
    $Groups = $DB->query('Select GroupName FROM ' . MySQL_Pre . 'SMS_Groups');

    return $Groups;
  }

  static function getContactGroups($ContactID) {
    $DB = new MySQLiDBHelper();
    //$DB->where('ContactID', $ContactID);
    //$Groups = $DB->get(MySQL_Pre . 'SMS_GroupDetails');
    $Groups = $DB->get(MySQL_Pre . 'SMS_Groups');

    return $Groups;
  }

  public function setGroup($GroupName) {
    $DB = new MySQLiDBHelper();
    $DB->where('GroupName', $GroupName);
    $Group = $DB->get(MySQL_Pre . 'SMS_Groups');
    //TODO Check if Group Exists
    $this->GroupID = $Group[0]['GroupID'];
  }

  function CreateGroup($GName) {
    $DB                      = new MySQLiDBHelper();
    $insertData['GroupName'] = $GName;
    $GroupID                 = $DB->insert(MySQL_Pre . 'SMS_Groups', $insertData);

    return $GroupID;
  }

  function addMember($ContactID) {
    $DB                      = new MySQLiDBHelper();
    $insertData['ContactID'] = $ContactID;
    $insertData['GroupID']   = $this->getGroupID();
    $GroupID                 = $DB->insert(MySQL_Pre . 'SMS_GroupDetails', $insertData);

    return $GroupID;
  }

  public function getGroupID() {
    return $this->GroupID;
  }

  function delMember($ContactID) {
    $DB                      = new MySQLiDBHelper();
    $insertData['ContactID'] = $ContactID;
    $insertData['GroupID']   = $this->getGroupID();
    $GroupID                 = $DB->where('GroupID', $this->getGroupID())
      ->where('ContactID', $ContactID)
      ->delete(MySQL_Pre . 'SMS_GroupDetails');

    return $GroupID;
  }
}
