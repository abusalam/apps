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
class AttendanceAPI extends AndroidAPI {

  /**
   * Attendance Register: Show Attendance for the User of Attendance Register
   *
   * Request:
   *   JSONObject={"API":"AR",
   *               "MDN":"8348691719"}
   *
   * Response:
   *    JSONObject={"API":true,
   *               "DB":298,
   *               "MSG":"Loaded Successfully!",
   *               "ET":2.0987,
   *               "ST":"Wed 20 Aug 08:31:23 PM"}
   */
  protected function AR() {
    $AuthUser = new AuthOTP();
    if ($AuthUser->authenticateUser($this->Req->MDN, $this->Req->OTP)
      OR $this->getNoAuthMode()
    ) {
      if($this->Req->MDN=="8348691719"){
        $DB                = new MySQLiDBHelper();
	$Data		   = array();
	//array_push($Data, $DB->get(MySQL_Pre . 'AR_View'));
	//array_push($Data, $DB->get(MySQL_Pre . 'SMS_NoDefsUsage'));
	$Data['AR']	   = $DB->get(MySQL_Pre . 'AR_View');
	$this->Resp['DB']  = array();
        array_push($this->Resp['DB'], $Data);

        $this->Resp['API'] = true;
        $this->Resp['MSG'] = 'Loaded Successfully!';
      } else {
        $this->Resp['API'] = false;
        $this->Resp['MSG'] = 'NA';
      }

    } else {
      $this->Resp['API'] = false;
      $this->Resp['MSG'] = 'NA';
    }
    //$this->setExpiry(3600);
    unset($DB);
    unset($tableData);
  }
}
