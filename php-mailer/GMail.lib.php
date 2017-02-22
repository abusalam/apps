<?php

if (file_exists(__DIR__ . '/config.gmail.php')) {
  require_once __DIR__ . '/config.gmail.php';
} else {
  require_once __DIR__ . '/config.sample.gmail.php';
}

function GMailSMTP($AppEMail, $AppName, $Subject, $Body, $TxtBody = 'View this page in HTML', $ReplyTo = GMail_UserID, $ReplyToUserName = UserName) {
  /**
   * PHPMailer Object Instance
   */
  $eMail = new PHPMailer();
  if (UseSMTP === TRUE) {
    $eMail->IsSMTP();
  }

  /**
   * Enable SMTP debugging
   *
   * 0 = off (for production use)
   * 1 = client messages
   * 2 = client and server messages
   */
  $eMail->SMTPDebug = 0;
  $eMail->Debugoutput = 'html';             //Ask for HTML-friendly debug output

  $eMail->Host = "smtp.gmail.com";          // sets GMAIL as the SMTP server
  $eMail->Port = 587;                       // set the SMTP port
  $eMail->SMTPSecure = "tls";               // sets the prefix to the server
  $eMail->SMTPAuth = true;                  // enable SMTP authentication

  $eMail->Username = GMail_UserID;          // GMAIL username
  $eMail->Password = GMail_Pass;            // GMAIL password

  $eMail->SetFrom(GMail_UserID, UserName);  //Set who the message is to be sent from
  $eMail->AddReplyTo($ReplyTo, $ReplyToUserName); //Set an alternative reply-to address
  $eMail->AddAddress($AppEMail, $AppName);  //Set who the message is to be sent to

  $eMail->Subject = $Subject;
  $eMail->MsgHTML($Body);
  $eMail->AltBody = $TxtBody; //Text Body
  $eMail->WordWrap = 50;                    // set word wrap
  //$eMail->AddAttachment("/path/to/file.zip");             // attachment
  //$eMail->AddAttachment("/path/to/image.jpg", "new.jpg"); // attachment

  $eMail->IsHTML(true); // send as HTML
  if (!$eMail->Send()) {
    $Resp['Status'] = "Mailer Error: " . $eMail->ErrorInfo;
    $Resp['Sent'] = FALSE;
  } else {
    $Resp['Status'] = "Message has been sent";
    $Resp['Sent'] = TRUE;
  }
  $Resp['IP'] = "Requested From:" . $_SERVER['REMOTE_ADDR'];
  return json_encode($Resp);
}

if ($_SERVER["REMOTE_ADDR"] === AllowedIP) {
  echo GMailSMTP(GetVal($_REQUEST, 'AppEmail'), GetVal($_REQUEST, 'AppName'), GetVal($_REQUEST, 'Subject'), GetVal($_REQUEST, 'Body'));
}
?>