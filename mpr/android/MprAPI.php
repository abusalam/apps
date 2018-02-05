<?php
require_once(__DIR__ . '/../../android/AndroidAPI.php');

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
class MprAPI extends AndroidAPI {

  /**
   * User Schemes: Retrieve all the Schemes for the User
   *
   * Request:
   *   JSONObject={"API":"US",
   *               "MDN":"35",
   *               "OTP":"123456"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":[{"SN":"BRGF","ID":"1"},{"SN":"MPLADS","ID":"5"}],
   *               "MSG":"All Schemes Loaded",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function US() {
    if (!$this->checkPayLoad(array('MDN', 'OTP'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      $DB = new MySQLiDBHelper();
      $DB->where('UserMapID', $AuthUser->getUserMapID());
      $Schemes = $DB->query('Select `SchemeName` as `SN`, `SchemeID` as `ID` FROM '
        . MySQL_Pre . 'MPR_ViewWorkerSchemes');
      if (count($Schemes) == 0) {
        $Schemes = $DB->query('Select `SchemeName` as `SN`, `SchemeID` as `ID` FROM '
          . MySQL_Pre . 'MPR_Schemes');
      }
      $this->Resp['DB']  = $Schemes;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Total Schemes: ' . count($Schemes);
      //$this->setExpiry(3600);
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP';
    }
    unset($DB);
    unset($Schemes);
  }

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
        case 'SID':
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          } else {
            if (!preg_match('/^[0-9]*$/', $this->Req->$Param)) {
              $this->Resp['MSG'] = "Invalid Scheme ID:" . $this->Req->$Param;
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
   * Scheme Funds: Retrieve all the Funds for the Schemes
   *
   * Request:
   *   JSONObject={"API":"SF",
   *               "MDN":"9876543210",
   *               "OTP":"123456"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":[{"SN":"IAP","F":null,"E":null,"B":null},
   *                    {"SN":"PUP","F":"9,00,00,000","E":"8,70,02,899","B":"29,97,101"}],
   *               "MSG":"All Schemes Loaded",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function SF() {
    if (!$this->checkPayLoad(array('MDN', 'OTP'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      $DB = new MySQLiDBHelper();
      $DB->where('UserMapID', $AuthUser->getUserMapID());
      $Schemes = $DB->query('Select `SchemeName` as `SN`, '
        . '`Funds` as `F`, `Expenses` as `E`, `Balance` as `B` FROM '
        . MySQL_Pre . 'MPR_ViewUserFunds');
      if (count($Schemes) == 0) {
        $Schemes = $DB->query('Select `SchemeName` as `SN`,'
          . '`Funds` as `F`,`Expense` as `E`,`Balance` as `B` '
          . 'from ' . MySQL_Pre . 'MPR_ViewSchemeWiseFunds');
      }
      $this->Resp['DB']  = $Schemes;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Total Schemes: ' . count($Schemes);
      //$this->setExpiry(3600);
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP';
    }
    unset($DB);
    unset($Schemes);
  }

  /**
   * Scheme Users: Retrieve all the Users for the Scheme
   *
   * Request:
   *   JSONObject={"API":"SU",
   *               "MDN":"9876543210",
   *               "OTP":"123456",
   *               "SID":"17"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":[{"UN":"DANTAN-II","ID":2,"M":null,"F":"6,000","B":"6,000","S":"Schemes"},
   *                  {"UN":"GOPIBALLAVPUR-II","ID":6,"M":"9674042595","F":"0","B":"0","S":"Schemes"}],
   *               "MSG":"All Users Loaded",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function SU() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'SID'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      $DB = new MySQLiDBHelper();
      $DB->where('UserMapID', $AuthUser->getUserMapID());
      $DB->where('SchemeID', $this->Req->SID);
      $Users = $DB->query('Select `UserName` as `UN`, `UserMapID` as `ID`,`MobileNo` as `M`, '
        . ' `Funds` as `F`, `Balance` as `B`, \'Schemes\' as `S` FROM '
        . MySQL_Pre . 'MPR_ViewUserFunds');
      if (count($Users) == 0) {
        $DB->where('SchemeID', $this->Req->SID);
        $Users = $DB->query('Select `UserName` as `UN`, `UserMapID` as `ID`,`MobileNo` as `M`, '
          . ' `Funds` as `F`, `Balance`  as `B`, \'Schemes\' as `S` FROM '
          . MySQL_Pre . 'MPR_ViewUserFunds');
      }
      $this->Resp['DB']  = $Users;
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Total Users: ' . count($Users);
      //$this->setExpiry(3600);
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP';
    }
    unset($DB);
    unset($Users);
  }

  /**
   * User Works: Retrieve all the Works for the User for a particular Scheme
   *
   * Request:
   *   JSONObject={"API":"UW",
   *               "MDN":"9876543210",
   *               "OTP":"123456",
   *               "SID":"5"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":[{"SN":"BRGF","ID":"1"},{"SN":"MPLADS","ID":"5"}],
   *               "MSG":"All Schemes Loaded",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function UW() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'SID'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      $DB = new MySQLiDBHelper();
      $DB->where('UserMapID', $AuthUser->getUserMapID());
      $DB->where('SchemeID', $this->Req->SID);
      $UserWorks = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
      $AllowEdit = true;
      if (count($UserWorks) == 0) {
        $DB->where('CtrlMapID', $AuthUser->getUserMapID());
        $DB->where('SchemeID', $this->Req->SID);
        $UserWorks = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
        $AllowEdit = false;
      }
      if (count($UserWorks) == 0) {
        $DB->where('SchemeID', $this->Req->SID);
        $UserWorks = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
        $AllowEdit = false;
      }
      $this->Resp['DB']             = $UserWorks;
      $this->Resp['Editable'] = $AllowEdit;
      $this->Resp['API']            = true;
      $this->Resp['MSG']            = 'Total Works : ' . count($UserWorks);
      //$this->setExpiry(3600);
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP';
    }
    unset($DB);
    unset($UserWorks);
  }

  /**
   * Update Progress: Update Progress of Works for the User for a particular
   * Work
   *
   * Request:
   *   JSONObject={"API":"UP",
   *               "MDN":"9876543210",
   *               "OTP":"987654",
   *               "WID":"5",
   *               "EA":"35000",
   *               "P":"90",
   *               "R":"Some Remarks"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":298,
   *               "MSG":"Updated Successfully!",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function UP() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'WID', 'EA', 'P', 'R'))) {
      return false;
    }
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      $DB = new MySQLiDBHelper();
      $DB->where('UserMapID', $AuthUser->getUserMapID());
      $DB->where('WorkID', $this->Req->WID);
      $UserWorks = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');

      if (count($UserWorks) > 0) {
        $Balance = intval(str_replace(",", "", $UserWorks[0]['Balance']));
        if ($Balance >= $this->Req->EA) {
          $tableData['WorkID']            = $this->Req->WID;
          $tableData['ExpenditureAmount'] = $this->Req->EA;
          $tableData['Progress']          = $this->Req->P;
          $tableData['ReportDate']        = date("Y-m-d", time());
          $tableData['Remarks']           = $this->Req->R;
          $tableData['MobileNo']          = $this->Req->MDN;

          $ProgressID = $DB->insert(MySQL_Pre . 'MPR_Progress', $tableData);

          if ($ProgressID) {
            $DB->where('WorkID', $this->Req->WID);
            $UserWorks         = $DB->get(MySQL_Pre . 'MPR_ViewUserWorks');
            $this->Resp['DB']  = $UserWorks;
            $this->Resp['API'] = true;
            $this->Resp['MSG'] = 'Updated Successfully!';
          } else {
            $this->Resp['API'] = false;
            $this->Resp['MSG'] = 'Unable To Update Progress.';
          }
        } else {
          $this->Resp['API'] = false;
          $this->Resp['MSG'] = 'Insufficient Balance.';
        }
      } else {
        $this->Resp['API'] = false;
        $this->Resp['MSG'] = 'WorkID:' . $this->Req->WID . ' is Not Assigned To You.';
      }
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP';
    }
    unset($DB);
    unset($tableData);
  }
}