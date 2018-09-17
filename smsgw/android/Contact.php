<?php

class Contact {

  static function getContactByMobileNo($MobileNo) {
    $DB = new MySQLiDBHelper();
    $DB->where('MobileNo', $MobileNo);
    $Contacts = $DB->get(MySQL_Pre . 'SMS_GroupWiseContacts');

    //print_r($Contacts);
    return $Contacts;
  }

  function createContact($Mobile, $Name, $Designation) {
    $DB                        = new MySQLiDBHelper();
    $insertData['ContactName'] = $Name;
    $insertData['Designation'] = $Designation;
    $insertData['MobileNo']    = $Mobile;
    $ContactID                 = $DB->insert(MySQL_Pre . 'SMS_Contacts', $insertData);

    return $ContactID;
  }

  function updateContact($Mobile, $Name, $Designation) {
    $DB                        = new MySQLiDBHelper();
    $insertData['ContactName'] = $Name;
    $insertData['Designation'] = $Designation;
    $DB->where('MobileNo', $Mobile);
    $ContactID = $DB->update(MySQL_Pre . 'SMS_Contacts', $insertData);
    return $ContactID;
  }

  function getAllContacts() {
    $DB       = new MySQLiDBHelper();
    $Contacts = $DB->get(MySQL_Pre . 'SMS_ViewContacts');
    print_r($Contacts);

    return $Contacts;
  }

  function getContactByGroup($Gid) {
    $DB = new MySQLiDBHelper();
    $DB->where('GroupID', $Gid);
    $Contacts = $DB->get(MySQL_Pre . 'SMS_ViewContacts');

    //print_r($Contacts);
    return $Contacts;
  }

  function getGroupMembers($GroupName) {
    $Group = new Group();
    $Group->setGroup($GroupName);
    $Gid = $Group->getGroupID();
    $DB  = new MySQLiDBHelper();
    $DB->where('GroupID', $Gid);
    $Contacts = $DB->get(MySQL_Pre . 'SMS_GroupMembers');

    //print_r($Contacts);
    return $Contacts;
  }

  function countContactByGroup($GroupName) {
    $Group = new Group();
    $Group->setGroup($GroupName);
    $Gid = $Group->getGroupID();
    $DB  = new MySQLiDBHelper();
    $DB->where('GroupID', $Gid);
    $s = $DB->get(MySQL_Pre . 'SMS_ViewContacts');
    $n = count($s);

    return $n;
  }

}
