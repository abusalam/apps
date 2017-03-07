<?php

class Group {

  protected $GroupID;

  public function setGroup($GroupName) {
    $DB = new MySQLiDBHelper();
    $DB->where('GroupName', $GroupName);
    $Group         = $DB->get(MySQL_Pre . 'SMS_Groups');
    $this->GroupID = $Group[0]['GroupID'];
  }

  public function getGroupID() {
    return $this->GroupID;
  }

  function CreateGroup($GName) {
    $DB                      = new MySQLiDBHelper();
    $insertData['GroupName'] = $GName;
    $GroupID                 = $DB->insert(MySQL_Pre . 'SMS_Groups', $insertData);

    return $GroupID;
  }

  static function getAllGroups() {
    $DB     = new MySQLiDBHelper();
    $Groups = $DB->query('Select GroupName FROM ' . MySQL_Pre . 'SMS_Groups');

    return $Groups;
  }

  static function getContactGroups($ContactID) {
    $DB     = new MySQLiDBHelper();
    $DB->where('ContactID', $ContactID);
    $Groups = $DB->get(MySQL_Pre . 'SMS_GroupDetails');

    return $Groups;
  }

  function addMember($ContactID) {
    $DB                      = new MySQLiDBHelper();
    $insertData['ContactID'] = $ContactID;
    $insertData['GroupID']   = $this->getGroupID();
    $GroupID                 = $DB->insert(MySQL_Pre . 'SMS_GroupDetails', $insertData);

    return $GroupID;
  }

  function delMember($ContactID) {
    $DB                      = new MySQLiDBHelper();
    $insertData['ContactID'] = $ContactID;
    $insertData['GroupID']   = $this->getGroupID();
    $GroupID                 = $DB->where('GroupID',$this->getGroupID())->where('ContactID',$ContactID)->delete(MySQL_Pre . 'SMS_GroupDetails');

    return $GroupID;
  }
}
