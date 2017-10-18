<?php
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

require_once(__DIR__ . '/AuthOTP.php');
require_once(__DIR__ . '/../smsgw/smsgw.inc.php');

class AndroidAPI {
  protected $Req;
  protected $Resp;
  private $Expiry;
  private $NoAuthMode;
  private $IntervalRU;

  function __construct($jsonData, $mNoAuthMode = false, $IntervalRU = 3600) {
    $this->IntervalRU = $IntervalRU; // Default Register Interval 1 Hour
    $this->Resp['ET'] = microtime();
    $this->Expiry     = null;
    $this->Req        = $jsonData;
    $this->setNoAuthMode($mNoAuthMode);
  }

  function __invoke() {
    if (property_exists($this->Req, "API")) {
      $this->setCallAPI($this->Req->API);
    } else {
      $this->Resp['MSG'] = "Invalid API Syntax!";
    }
  }

  private function setCallAPI($CallAPI) {
    if (method_exists($this, $CallAPI)) {
      $this->$CallAPI();
    } else {
      /**
       * Unknown API Call
       */
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid API' . $CallAPI;
    }
  }

  function __unset($name) {
    $this->sendResponse();
  }

  /**
   * Input validation for API PayLoads
   *
   * Following keys are checked in this class: ['MDN','OTP','OTP1','OTP2']
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

    foreach ($Params as $Param) {

      switch ($Param) {
        case 'MDN':
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          } else {
            if (!preg_match('/^[789]\d{9}$/', $this->Req->$Param)) {
              $this->Resp['MSG'] = "Invalid Mobile Number";
              return false;
            }
          }
          break;

        case 'OTP':
        case 'OTP1':
        case 'OTP2':
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          } else {
            if (!preg_match('/^\d{6}$/', $this->Req->$Param)) {
              $this->Resp['MSG'] = "OTP should be 6 digits only";
              return false;
            }
          }
          break;

        case 'IMEI':
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          } else {
            if (!preg_match('/^\d{15}$/', $this->Req->$Param)) {
              $this->Resp['MSG'] = "IMEI should be 15 digits only";
              return false;
            }
          }
          break;

        case 'IP':
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          } else {
            if (!preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $this->Req->$Param)) {
              $this->Resp['MSG'] = "IP should be digits in valid range and in xxx.xxx.xxx.xxx format!";
              return false;
            }
          }
          break;

        default:
          if (!property_exists($this->Req, $Param)) {
            $this->Resp['MSG'] = "Invalid PayLoad";
            return false;
          }
      }
    }
    return true;
  }

  protected function sendResponse() {
    //$this->Resp['json'] = $this->Req; //TODO: Remove for Production
    $this->Resp['ET'] = microtime() - $this->Resp['ET'];
    $DateFormat       = 'D d M g:i:s A';
    $this->Resp['ST'] = date($DateFormat, time());

    $JsonResp = json_encode($this->Resp);

    header('Content-Type: application/json');
    header('Content-Length: ' . strlen($JsonResp));
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $this->getExpiry()));
    echo $JsonResp;
  }

  public function getExpiry() {
    /**
     * Important: Tells volley not to cache the response
     */
    if (($this->Expiry == null) OR
      ($this->getNoAuthMode() == false)
    ) {
      /**
       * Never Cache Authenticated Response
       */
      $Expires = time() - 3600;
    } else {
      $Expires = time() + $this->Expiry;
    }


    return $Expires;
  }

  protected function setExpiry($Expiry) {
    $this->Expiry = $Expiry;
  }

  protected function getNoAuthMode() {
    return $this->NoAuthMode;
  }

  protected function setNoAuthMode($NoAuthMode = true) {
    $this->NoAuthMode = $NoAuthMode;
  }

  function __destruct() {
    $this->sendResponse();
  }

  /**
   * Register User: Register User with Mobile No. to get the Secret Key for
   * HOTP
   *
   * TODO Important: Store New Credentials in an Alternate field for validation
   * against OTP
   *
   * Request:
   *   JSONObject={"API":"RU",
   *               "MDN":"9876543210"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB": // Unused till now
   *               "MSG":"Key Sent to Mobile No. 9876543210",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   *
   */
  protected function RU() {
    if (!$this->checkPayLoad(array('MDN'))) {
      return;
    };
    $this->Resp['SendSMS'] = false;
    $DB                    = new MySQLiDBHelper();
    $Data['MobileNo']      = $this->Req->MDN;
    $DB->where('MobileNo', $Data['MobileNo']);
    $Profile = $DB->get(MySQL_Pre . 'APP_Users');
    if (count($Profile) == 0) {
      $DB->insert(MySQL_Pre . 'APP_Users', $Data);
      $this->Resp['SendSMS'] = true;
    } elseif ((time() - strtotime($Profile[0]['LastAccessTime'])) > $this->IntervalRU) {
      $this->Resp['SendSMS']     = true;
    } else {
      $this->Resp['TimeElapsed'] = $Profile[0]['LastAccessTime'];
    }
    if ($this->Resp['SendSMS'] === true) {
      $AuthUser = new AuthOTP(AuthOTP::TOKEN_DATA_TEMP);
      $AuthUser->deleteUser($this->Req->MDN);
      $SecretKey = $AuthUser->setUser($this->Req->MDN, "TOTP");
      SMSGW::SendSMS('Activation Key: ' . $SecretKey
        . "\nValid Till: " . date("D d M g:i:s A", time() + $this->IntervalRU), $this->Req->MDN);
      $this->Resp['MSG'] = "Please enter the Activation Key Sent to Mobile No. " . $this->Req->MDN;
    } else {
      $this->Resp['MSG'] = "Please enter the Activation Key received on Mobile No. "
        . $this->Req->MDN . " \nAfter: " . $this->Resp['TimeElapsed'];
    }
    $this->Resp['API']     = true;
    $fieldData['MobileNo'] = $this->Req->MDN;
    $DB->insert(MySQL_Pre . 'APP_Register', $fieldData);
  }

  /**
   * OTP Test: Test User OTP against Registration Data
   * and if found valid update user credentials with new data
   *
   * Request:
   *   JSONObject={"API":"OT",
   *               "MDN":"9876543210",
   *               "OTP":"123456"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB": {'KeyUpdated':1,
   *                      "USER":{"UserName":"John Smith",
   *                              "Designation":"Operator",
   *                              "eMailID":"jsmith@gmail.com"}
   *                      }
   *               "MSG":"Mobile No. 9876543210 is Registered Successfully.",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function OT() {
    if (!$this->checkPayLoad(array('MDN', 'OTP'))) {
      return;
    };
    $AuthUser = new AuthOTP(AuthOTP::TOKEN_DATA_TEMP);
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP) OR $this->getNoAuthMode()) {
      $DB = new MySQLiDBHelper();

      $this->Resp['DB']['KeyUpdated'] = $DB->where('MobileNo', $this->Req->MDN)
        ->ddlQuery('Update ' . MySQL_Pre . 'APP_Users Set UserData=TempData');

      $DB->where('MobileNo', $this->Req->MDN);
      $this->Resp['DB']['USER'] = $DB->query('Select `UserMapID`, `UserID` as `eMailID`,'
        . ' `UserName` as `Designation`, `DisplayName` FROM ' . MySQL_Pre . 'Users');

      $UserData['UserMapID']=$this->Resp['DB']['USER'][0]['UserMapID'];
      //TODO Import the UserData from Users table into APP_Users

      $DB->where('MobileNo', $this->Req->MDN)
        ->update(MySQL_Pre . 'APP_Users', $UserData);

      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Mobile No. ' . $this->Req->MDN . ' is Registered Successfully!'
        . ' Now you can start using the Project AIO App.';
    } else {
      //$this->Resp['URL'] = $AuthUser->createURL($this->Req->MDN);
      $this->Resp['DB'] = "Authentication Failed!";
      //. $AuthUser->oath_hotp($AuthUser->getKey($this->Req->MDN), $this->Req->TC);
      $this->Resp['API'] = false;
      $DateFormat = 'g:i:s A';
      $this->Resp['MSG'] = 'Invalid OTP. Please check your date time then retry.'
        . ' Server Time: ' . date($DateFormat, time());
    }
  }

  /**
   * Sync Profile: Sync User Profile from Registration Data on Server
   *
   * Request:
   *   JSONObject={"API":"SP",
   *               "MDN":"9876543210",
   *               "OTP":"234569",
   *               "OTP1":"123456",
   *               "OTP2":"612345"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB": {"UserName":"John Smith",
   *                      "Designation":"Operator",
   *                      "eMailID":"jsmith@gmail.com"
   *                     }
   *
   *               "MSG":"Profile Downloaded Successfully.",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function SP() {
    if (!$this->checkPayLoad(array('MDN', 'OTP', 'OTP1', 'OTP2'))) {
      return;
    };
    $AuthUser = new AuthOTP();
    $ReSynced = $AuthUser->resyncCode($this->Req->MDN, $this->Req->OTP1, $this->Req->OTP2);
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      $DB = new MySQLiDBHelper();
      $DB->where('MobileNo', $this->Req->MDN);
      $this->Resp['DB']  = $DB->get(MySQL_Pre . 'Users');
      $this->Resp['API'] = true;
      $this->Resp['MSG'] = 'Synchronized Successfully.';
    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'Invalid OTP';
    }
  }

  /**
   * Access Logs: Log All Access from the Android App
   *
   * Request:
   *   JSONObject={"API":"AL",
   *               "MDN":"9876543210",
   *               "IP":"164.100.105.34",
   *               "IMEI":"3214352671528765"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "MSG":"Access Logged Successfully.",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function AL() {
    if (!$this->checkPayLoad(array('MDN', 'IP', 'IMEI'))) {
      return;
    };
    $DB               = new MySQLiDBHelper();
    $Data['LocalIP']  = $this->Req->IP;
    $Data['MobileNo'] = $this->Req->MDN;
    $Data['IMEI']     = $this->Req->IMEI;
    $Data['IP']       = $_SERVER['REMOTE_ADDR'];

    $DB->insert(MySQL_Pre . 'APP_Logs', $Data);

    $this->Resp['API'] = true;
    $this->Resp['MSG'] = 'Access Logged Successfully!';
  }
}
