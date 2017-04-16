<?php

/**
 * Gustavo Granados
 * code is poetry
 */
class MailManager
{

  private static $return = CoreConfig::MAIL_RETURN;
  private static $from = CoreConfig::MAIL_FROM;
  private static $host = CoreConfig::MAIL_HOST;
  private static $port = CoreConfig::MAIL_PORT;
  private static $username = CoreConfig::MAIL_USERNAME;
  private static $password = CoreConfig::MAIL_PASSWORD;
  private static $auth = CoreConfig::MAIL_AUTH;

  private static $lastError = null;
  private static $htmlFormat = true;

  /**
   * Get last error
   *
   * @return string
   */
  public static function getLastError()
  {
    return MailManager::$lastError;
  }

  /**
   * set if the email is a html format
   *
   * @param bool $value
   */
  public static function setHtmlFormat($value)
  {
    MailManager::$htmlFormat = $value;
  }

  /**
   * Method that allows separate by comma a string and convert to array
   *
   * @param string $string
   *
   * @return array
   */
  private static function getArrayToSendEmail($string)
  {
    $array = array();
    $text = '';
    for($index = 0; $index < strlen($string); $index++)
    {
      if($string[$index] != ',')
      {
        $text .= $string[$index];
      }
      else
      {
        array_push($array, $text);
        $text = '';
      }
    }

    return $array;
  }

  /**
   * Advanced method to send emails with attachments
   *
   * @param $recipients
   * @param $subject
   * @param $body
   * @param bool $attachments
   * @param null $from
   *
   * @return bool
   */
  public static function sendAdvancedEmail($recipients, $subject, $body, $attachments = false, $from = null)
  {
    $to = "";
    $cc = "";
    $bcc = "";
    if(is_array($recipients))
    {
      $to = $recipients['To'];
      $cc = $recipients['Cc'];
      $bcc = $recipients['Bcc'];
    }
    else
    {
      $to = $recipients;
    }

    if(Util::isDEV())
    {
      $to = CoreConfig::MAIL_DEV;
    }
    if(is_array($recipients))
    {
      $recipients['To'] = $to;
    }

    $mailer = new PHPMailer();
    if(MailManager::$htmlFormat)
    {
      $mailer->isHTML(true);
      $mailer->Body = $body;
    }
    else
    {
      $mailer->Body = $body;
    }

    if($attachments && is_array($attachments))
    {
      //Attach multiple files one by one
      if(!empty($attachments['tmp_name'][0]))
      {
        for($index = 0; $index < count($attachments['tmp_name']); $index++)
        {
          $uploadFile = tempnam(sys_get_temp_dir(), sha1($attachments['name'][$index]));
          $filename = $attachments['name'][$index];
          if(move_uploaded_file($attachments['tmp_name'][$index], $uploadFile))
          {
            $mailer->addAttachment($uploadFile, $filename);
          }
        }
      }
    }

    $mailer->setFrom($from ? $from : MailManager::$from);
    $mailer->Subject = $subject;

    $arrayTo = self::getArrayToSendEmail($to);
    foreach($arrayTo as $t)
    {
      $mailer->addAddress($t);
    }

    if($cc)
    {
      $arrayCC = self::getArrayToSendEmail($cc);
      foreach($arrayCC as $c)
      {
        $mailer->addCC($c);
      }
    }

    if($bcc)
    {
      $arrayBCC = self::getArrayToSendEmail($bcc);
      foreach($arrayBCC as $b)
      {
        $mailer->addCC($b);
      }
    }

    $mailer->isSMTP();
    $mailer->Host = MailManager::$host;
    $mailer->Port = MailManager::$port;
    $mailer->SMTPAuth = MailManager::$auth;
    $mailer->Username = MailManager::$username;
    $mailer->Password = MailManager::$password;

    if(!$mailer->send())
    {
      MailManager::$lastError = 'Email was not sent';

      return false;
    }

    return true;
  }

  /**
   * Standard method to send emails using php config file
   *
   * @param $to
   * @param $subject
   * @param $body
   *
   * @return bool
   */
  private static function sendStandardEmailNoAttachments($to, $subject, $body)
  {
    $headers = 'MIME-Version: 1.0'."\r\n";
    if(MailManager::$htmlFormat)
    {
      $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    }
    else
    {
      $headers .= 'Content-type: text/plain; charset=iso-8859-2'."\r\n";
    }
    $headers .= 'From: '.MailManager::$from."\r\n";
    if(Util::isDEV())
    {
      $to = CoreConfig::MAIL_DEV;
    }
    if(@mail($to, $subject, $body, $headers, "-f".MailManager::$return))
    {
      return true;
    }

    MailManager::$lastError = 'Email was not sent';

    return false;
  }

  /**
   * send standard email using default php function
   *
   * @param $to
   * @param $subject
   * @param $message
   * @param bool $files
   *
   * @return bool
   */
  private static function sendStandardEmail($to, $subject, $message, $files = false)
  {
    ini_set('sendmail_from', CoreConfig::MAIL_FROM);
    ini_set('SMTP', CoreConfig::MAIL_HOST);
    ini_set('smtp_port', CoreConfig::MAIL_PORT);

    if(!$files)
    {
      return MailManager::sendStandardEmailNoAttachments($to, $subject, $message);
    }

    if(Util::isDEV())
    {
      $to = CoreConfig::MAIL_DEV;
    }

    $isAttach = false;
    $textMessage = strip_tags(nl2br($message), "<br>");
    $htmlMessage = nl2br($message);
    $fromEmail = strip_tags(MailManager::$from);

    $boundary1 = rand(0, 9)."-".rand(10000000000, 9999999999)."-".rand(10000000000, 9999999999)."=:".rand(10000, 99999);
    $boundary2 = rand(0, 9)."-".rand(10000000000, 9999999999)."-".rand(10000000000, 9999999999)."=:".rand(10000, 99999);

    //get attachments
    for($index = 0; $index < count($files['name']); $index++)
    {
      if(is_uploaded_file($files['tmp_name'][$index]) && !empty($files['size'][$index]) && !empty($files['name'][$index]))
      {
        $isAttach = true;
        $handle = fopen($files['tmp_name'][$index], 'rb');
        $f_contents = fread($handle, $files['size'][$index]);
        $fileAttachment[] = chunk_split(base64_encode($f_contents));
        fclose($handle);
        $fileType[] = $files['type'][$index];
        $fileName[] = $files['name'][$index];
      }
    }

    //Email without Attachment
    //set headers
    $headers = <<<AKAM
From: <$fromEmail>
Reply-To: $fromEmail
MIME-Version: 1.0
Content-Type: multipart/alternative;
    boundary="$boundary1"
AKAM;

    //set body
    $body = <<<AKAM
MIME-Version: 1.0
Content-Type: multipart/alternative;
    boundary="$boundary1"

This is a multi-part message in MIME format.

--$boundary1
Content-Type: text/plain;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$textMessage
--$boundary1
Content-Type: text/html;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$htmlMessage

--$boundary1--
AKAM;

    //HTML Email WIth Multiple Attachment
    if($isAttach)
    {

      $attachments = '';
      $headers = <<<AKAM
From:<$fromEmail>
Reply-To: $fromEmail
MIME-Version: 1.0
Content-Type: multipart/mixed;
    boundary="$boundary1"
AKAM;

      for($count = 0; $count < count($fileType); $count++)
      {
        $attachments .= <<<ATTA
--$boundary1
Content-Type: $fileType[$count];
    name="$fileName[$index]"
Content-Transfer-Encoding: base64
Content-Disposition: attachment;
    filename="$fileName[$count]"

$fileAttachment[$count]

ATTA;
      }

      $body = <<<AKAM
This is a multi-part message in MIME format.

--$boundary1
Content-Type: multipart/alternative;
    boundary="$boundary2"

--$boundary2
Content-Type: text/plain;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$textMessage
--$boundary2
Content-Type: text/html;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$htmlMessage

--$boundary2--

$attachments
--$boundary1--
AKAM;
    }

    //Sending Email
    $sentEmail = mail($to, $subject, $body, $headers);

    //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
    return $sentEmail ? true : false;
  }

  /**
   * Main method to send emails
   *
   * @param $recipients
   * @param $subject
   * @param $body
   * @param array $attachments
   *
   * @return bool
   */
  public static function sendEmail($recipients, $subject, $body, $attachments = false)
  {
    if(is_array($recipients))
    {
      $to = "";
      foreach($recipients as $email)
      {
        $to .= "$email,";
      }
    }
    else
    {
      $to = "$recipients";
    }

    MailManager::$lastError = null;

    if(CoreConfig::MAIL_STANDARD)
    {
      $result = MailManager::sendStandardEmail($to, $subject, $body, $attachments);
    }
    else
    {
      $result = MailManager::sendAdvancedEmail($to, $subject, $body, $attachments);
    }

    MailManager::$htmlFormat = true;

    return $result;
  }

  /**
   * Send Email
   *
   * @param $pTo
   * @param $pCC
   * @param $pBCC
   * @param $subject
   * @param $body
   * @param bool $attachments
   *
   * @return bool
   */
  public static function sendEmailReport($pTo, $pCC, $pBCC, $subject, $body, $attachments = false)
  {
    $recipients = array();
    if($pTo)
    {
      $recipients["To"] = $pTo;
    }
    if($pCC)
    {
      $recipients["Cc"] = $pCC;
    }
    if($pBCC)
    {
      $recipients["Bcc"] = $pBCC;
    }

    MailManager::$lastError = null;

    $result = MailManager::sendAdvancedEmail($recipients, $subject, $body, $attachments);

    MailManager::$htmlFormat = true;

    return $result;
  }

  /**
   * Get the list of email address to send the email
   *
   * @param array $emailGroups
   *
   * @return array
   */
  public static function getRecipients($emailGroups = array('Programmers'))
  {
    $recipients = array();
    if(count($recipients) == 0)
    {
      array_push($recipients, CoreConfig::MAIL_DEV); //default
    }

    return $recipients;
  }

  /**
   * Use this method to send a warning email
   *
   * @param string $className
   * @param string $methodName
   * @param string $description
   */
  public static function sendWarningEmail($className, $methodName, $description)
  {
    $body = "<b>Warning</b><br><br>";
    $body .= "<b>Class Name: </b> $className<br>";
    $body .= "<b>Method Name: </b> $methodName<br>";
    $body .= "<b>Description: </b> $description<br>";
    MailManager::sendEmail(MailManager::getRecipients(), "Warning", $body);
  }

  /**
   * Use this method to send a critical email, like db errors, or logic errors
   *
   * @param $subject
   * @param $description
   */
  public static function sendCriticalErrorEmail($subject, $description)
  {
    $body = "<b>Critical Error</b><br><br>";
    $body .= "<b>Description: </b> $description<br>";
    MailManager::sendEmail(MailManager::getRecipients(), $subject, $body);
  }

  /**
   * Use this method to send information, like summaries or process status
   *
   * @param string $subject
   * @param string $description
   */
  public static function sendInfoEmail($subject, $description)
  {
    $body = "<b>Information</b><br><br>";
    $body .= "<b>Description: </b> $description<br>";

    MailManager::sendEmail(MailManager::getRecipients(), $subject, $body);
  }

}

?>