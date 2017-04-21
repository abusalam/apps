<?php
require_once(__DIR__ . '/../../android/AndroidAPI.php');
require_once(__DIR__ . '/Message.php');
require_once(__DIR__ . '/Group.php');
require_once(__DIR__ . '/Contact.php');
require_once(__DIR__ . '/User.php');

/**
 * API Calls from a valid user from an Android System.
 *
 *
 * The Response JSONObject will Contain the following Top Level Nodes
 *
 * 1. $Resp['API'] => boolean Status of the API Call
 * 2. $Resp['DB'] => Data to be sent depending upon the Called API
 * 3. $Resp['MSG'] => Message to be displayed after API Call
 * 4. $Resp['ET'] => Execution Time of the Script in Seconds
 * 5. $Resp['ST'] => Server Time of during the API Call
 *
 * @example Sample API Call
 *
 * Request:
 *   JSONObject={"API":"AG",
 *               "MDN":"9876543210",
 *               "OTP":"987654"}
 *
 * Response:
 *    JSONObject={"API":true,
 *               "DB":[{"GRP":"All BDOs"},{"GRP":"All SDOs"}],
 *               "MSG":"Total Groups: 2",
 *               "ET":2.0987,
 *               "ST":"Wed 20 Aug 08:31:23 PM"}
 *
 */
class MessageAPI extends AndroidAPI {

  /**
   * Input validation for API PayLoads in this class
   *
   * Following keys are checked in the parent class:
   *      ['MDN','OTP','OTP1','OTP2','IMEI','IP']
   *
   * @param $Params
   * @return bool
   *
   * @example Sample function call
   *
   *        $this->checkPayLoad(array('MDN','OTP'))
   *
   */
  protected function checkPayLoad($Params) {

    $checkParams = array();

    foreach ($Params as $Param) {

      switch ($Param) {
        case 'CID':
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          } else {
            if (!preg_match('/^\d$/', $this->Req->$Param)) {
              $this->Resp['MSG'] = "Invalid Contact ID";
              return false;
            }
          }
          break;

        default:
          array_push($checkParams, $Param);
          break;
      }
    }
    return parent::checkPayLoad($checkParams);
  }

  /**
   * All Groups: Retrieve All Groups
   *
   * Request:
   *   JSONObject={"API":"AG",
   *               "MDN":"9876543210",
   *               "OTP":"987654"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":[{"GRP":"All BDOs"},{"GRP":"All SDOs"}],
   *               "MSG":"Total Groups: 2",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function AG() {
    $this->Resp['DB']  = Group::getAllGroups();
    $this->Resp['API'] = true;
    $this->Resp['MSG'] = 'All Groups Loaded';
    //$this->setExpiry(3600); // 60 Minutes
  }

  /**
   * Send SMS To a Group.
   *
   * Request:
   *   JSONObject={"API":"SM",
   *               "MDN":"9876543210",
   *               "TXT":"Hello",
   *               "GRP":"BDO",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":"Return Message ID".
   *               "MSG":"Message Sent",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function SM() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'GRP', 'TXT'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Msg               = new Message();
      $User              = new User($this->Req->MDN);
      $Mid               = $Msg->createSMS($User, $this->Req->TXT, $this->Req->GRP);
      $Contact           = new Contact();
      $count             = $Contact->countContactByGroup($this->Req->GRP);
      $this->Resp['DB']  = $Mid;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Message Sent to ' . $count
        . ' Contacts of ' . $this->Req->GRP . ' Group';
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Delivery Status of a Message
   *
   * Request:
   *   JSONObject={"API":"DS",
   *               "MDN":"9876543210",
   *               "MID":"123",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":"Return Message ID".
   *               "MSG":"Message Sent",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function DS() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'MID'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    $DB       = new MySQLiDB();
    $Data     = new MySQLiDBHelper();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $RowCount = $DB->do_sel_query("Select * from " . MySQL_Pre . "SMS_ViewDlrData");
      $Result   = array();
      for ($i = 0; $i < $RowCount; $i++) {
        $Row             = $DB->get_row();
        $Record['MsgID'] = $Row['MsgID'];

        $Record['MsgData']        = json_decode(htmlspecialchars_decode($Row['MsgData']), true);
        $MsgData['MessageID']     = $Record['MsgData']['a2wackid'];
        $MsgData['MobileNo']      = $Record['MsgData']['mnumber'];
        $MsgData['DlrStatus']     = $Record['MsgData']['a2wstatus'];
        $MsgData['CarrierStatus'] = $Record['MsgData']['carrierstatus'];
        $MsgData['SentOn']        = $Record['MsgData']['submitdt'];
        $MsgData['DeliveredOn']   = $Record['MsgData']['lastutime'];
        if ($MsgData['CarrierStatus'] == 'DELIVRD') {
          $MsgData['UnDelivered'] = 0;
        }
        $Data->where('MessageID', $MsgData['MessageID']);
        $Rows = $Data->get(MySQL_Pre . 'SMS_DlrReports');
        if (count($Rows) == 0) {
          $this->Resp['Data'] = $MsgData;
          $Updated            = $Data->insert(MySQL_Pre . "SMS_DlrReports", $MsgData);
        } else {
          $Data->where('UnDelivered', 1);
          $Data->where('MessageID', $MsgData['MessageID']);
          $Data->update(MySQL_Pre . "SMS_DlrReports", $MsgData);
          $Updated = 1;
        }
        if ($Updated > 0) {
          $Data->where('MsgID', $Record['MsgID']);
          $UpdateData['ReadUnread']    = 1;
          $this->Resp['DB']['Updated'] = $Data->update("SMS_Data", $UpdateData);
        }
        array_push($Result, $Record);
      }
      $DB->do_close();
      unset($Data);
      $this->Resp['DB']  = $Result;
      $this->Resp['API'] = true;
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Create a New Group
   *
   * Request:
   *   JSONObject={"API":"NG",
   *               "MDN":"9876543210",
   *               "GRP":"Test Group",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={
   *                "ET": 0.527576,
   *                "DB":
   *                    {
   *                      "GroupID": 1,
   *                      "GroupName": "Test Group"
   *                    },
   *                "API": true,
   *                "MSG": "Group Created with ID:1",
   *                "ST": "Tue 07 Mar 3:39:37 PM"
   *              }
   */
  protected function NG() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'GRP'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Group   = new Group();
      $GroupID = $Group->CreateGroup($this->Req->GRP);
      if ($GroupID > 0) {
        $GroupDB['GroupID']   = $GroupID;
        $GroupDB['GroupName'] = $this->Req->GRP;
        $this->Resp['DB']     = $GroupDB;
        $this->Resp['API']    = true;
        $this->Resp['MSG']    = 'Group Created with ID:' . $GroupID;
      } else {
        $this->Resp['API'] = false;
        $this->Resp['MSG'] = 'Unable to Create Group!';
      }

    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Get All Members in a Group
   *
   * Request:
   *   JSONObject={"API":"GM",
   *               "MDN":"9876543210",
   *               "GRP":"BDO",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":[
   *                      {
   *                        "ContactID": 186,
   *                        "ContactName": "Test Contact",
   *                        "Designation": "",
   *                        "GroupID": 9,
   *                        "MobileNo": "8348691719"
   *                      }
   *                ],
   *               "MSG":"Loaded 1 Contacts of DIO NIC Group",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function GM() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'GRP'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Contact           = new Contact();
      $count             = $Contact->countContactByGroup($this->Req->GRP);
      $Contacts          = $Contact->getGroupMembers($this->Req->GRP);
      $this->Resp['DB']  = $Contacts;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Loaded ' . $count
        . ' Contacts of ' . $this->Req->GRP . ' Group';
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Contact Groups: Retrieve All Contact Groups
   *
   * Request:
   *   JSONObject={"API":"CG",
   *               "MDN":"9876543210",
   *               "CID":"10",
   *               "OTP":"987654"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":[{"GRP":"All BDOs"},{"GRP":"All SDOs"}],
   *               "MSG":"Total Groups: 2",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function CG() {
    $this->Resp['DB']  = Group::getContactGroups($this->Req->CID);
    $this->Resp['API'] = true;
    $this->Resp['MSG'] = 'All Groups for this Contact loaded successfully';
    //$this->setExpiry(3600); // 60 Minutes
  }

  /**
   * Add new Contact
   *
   * Request:
   *   JSONObject={"API":"AC",
   *               "MDN":"9876543210",
   *               "MN":"9876543210",
   *               "NM":"Contact Name",
   *               "DG":"Designation",
   *               "GRP":"BDO",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":10,
   *               "MSG":"Added to BDO Group",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function AC() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'GRP', 'MN', 'NM', 'DG'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Contact   = new Contact();
      $ContactID = $Contact->createContact($this->Req->MN, $this->Req->NM, $this->Req->DG);
      if ($ContactID > 0) {
        $Group = new Group();
        $Group->setGroup($this->Req->GRP);
        $Gid               = $Group->addMember($ContactID);
        $this->Resp['DB']  = $Gid;
        $this->Resp['API'] = true;
        $this->Resp['MSG'] = 'Added to ' . $this->Req->GRP . ' Group';
      } else {
        $this->Resp['API'] = false;
        $this->Resp['MSG'] = 'Unable to add Contact!';
      }

    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Add Update Existing Contact
   *
   * Request:
   *   JSONObject={"API":"UC",
   *               "MDN":"9876543210",
   *               "MN":"9876543210",
   *               "NM":"Contact Name",
   *               "DG":"Designation",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":10,
   *               "MSG":"Contact Updated Successfully!",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function UC() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'MN', 'NM', 'DG'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Contact   = new Contact();
      $ContactID = $Contact->updateContact($this->Req->MN, $this->Req->NM, $this->Req->DG);
      if ($ContactID > 0) {
        $this->Resp['API'] = true;
        $this->Resp['MSG'] = 'Contact Updated Successfully!';
      } else {
        $this->Resp['API'] = false;
        $this->Resp['MSG'] = 'Unable to update Contact!';
      }

    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Add a Member to a Group
   *
   * Request:
   *   JSONObject={"API":"AM",
   *               "MDN":"9876543210",
   *               "CID":"20",
   *               "GRP":"BDO",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":10,
   *               "MSG":"Added to BDO Group",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function AM() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'GRP', 'CID'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Group = new Group();
      $Group->setGroup($this->Req->GRP);
      $Gid               = $Group->addMember($this->Req->CID);
      $this->Resp['DB']  = $Gid;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Added to ' . $this->Req->GRP . ' Group';
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }

  /**
   * Remove a Member from a Group
   *
   * Request:
   *   JSONObject={"API":"RM",
   *               "MDN":"9876543210",
   *               "CID":"20",
   *               "GRP":"BDO",
   *               "OTP":"987654"}
   *
   * Response:
   *   JSONObject={"API":true,
   *               "DB":10,
   *               "MSG":"Removed from BDO Group",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function RM() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'GRP', 'CID'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $Group = new Group();
      $Group->setGroup($this->Req->GRP);
      $Gid               = $Group->delMember($this->Req->CID);
      $this->Resp['DB']  = $Gid;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Removed from ' . $this->Req->GRP . ' Group';
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP ' . $this->Req->OTP;
    }
  }
}
