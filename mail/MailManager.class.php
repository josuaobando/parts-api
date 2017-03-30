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
   * Advanced method to send emails with attachments
   *
   * @param string $to
   * @param string $subject
   * @param string $body
   * @param array $attachments
   * @return bool
   */
  public static function sendAdvancedEmail($to, $subject, $body, $attachments = false)
  {
  	if (Util::isDEV())
    {
    	$to = CoreConfig::MAIL_DEV;
    }
	  
    $message = new Mail_mime();
    $message->setHTMLBody($body);
    if ($attachments && is_array($attachments))
    {
      foreach ($attachments as $attachment)
      {
        $message->addAttachment($attachment);
      }
    }
    $body = $message->get();
    
    $extraheaders = array();
    $extraheaders['From'] = MailManager::$from;
    $extraheaders['To'] = $to;
    $extraheaders['Subject'] = $subject;
    
    $headers = $message->headers($extraheaders);

    $smtpInfo = array();
    $smtpInfo['host'] = MailManager::$host;
    $smtpInfo['port'] = MailManager::$port;
    $smtpInfo['auth'] = MailManager::$auth;
    $smtpInfo['username'] = MailManager::$username;
    $smtpInfo['password'] = MailManager::$password;
    
    $smtp = Mail::factory('smtp', $smtpInfo);
    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail))
    {
      MailManager::$lastError = $mail->getMessage();
      return false;
    }
    
    return true;
  }
  
  /**
   * Standard method to send emails using php config file
   *
   * @param array $to
   * @param string $subject
   * @param string $body
   * @param array $attachments
   * @return bool
   */
  private static function sendStandardEmailNoAttachments($to, $subject, $body)
  {
  	
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    if (MailManager::$htmlFormat)
    {
    	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    }
    else 
    {
    	$headers .= 'Content-type: text/plain; charset=iso-8859-2' . "\r\n";
    }
    $headers .= 'From: ' . MailManager::$from . "\r\n";
    if (Util::isDEV())
    {
    	$to = CoreConfig::MAIL_DEV;
    }
    if (@mail($to, $subject, $body, $headers, "-f".MailManager::$return))
    {
    	return true;
    }
    
    MailManager::$lastError = 'Email was not sent';
    return false;
  }
  
  private static function sendStandardEmail($to, $subject, $body, $attachments = false)
  {
  	ini_set('sendmail_from', CoreConfig::MAIL_FROM);
  	ini_set('SMTP', CoreConfig::MAIL_HOST);
		ini_set('smtp_port', CoreConfig::MAIL_PORT);

  	if (!$attachments)
  	{
  		return MailManager::sendStandardEmailNoAttachments($to, $subject, $body);
  	}
  	
  	if (Util::isDEV())
    {
    	$to = CoreConfig::MAIL_DEV;
    }
  	
  	foreach ($attachments as $k=>$v)
  	{
  		$fileName = $k;
  		$file = $v;
  		break;
  	}
  	
		//create a boundary string. It must be unique
		//so we use the MD5 algorithm to generate a random hash
		$random_hash = md5(date('r', time()));
		
		//define the headers we want passed. Note that they are separated with \r\n
		$headers = "From: ".MailManager::$from . "\r\n";
		$headers .= "Reply-To: ".MailManager::$from . "\r\n";
		
		//add boundary string and mime type specification
		$headers .= "\r\nContent-Type: multipart/alternative; boundary=\"PHP-alt-".$random_hash."\""; 
		
		//read the atachment file contents into a string,
		//encode it with MIME base64,
		//and split it into smaller chunks
		$attachment = chunk_split(base64_encode(file_get_contents($file)));
		
		//define the body of the message.
		ob_start(); //Turn on output buffering
		?>

--PHP-alt-<?php echo $random_hash; ?>

Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?php
$bodyPlain = str_replace("<br />", "\r\n", $body);
$bodyPlain = str_replace("<br/>", "\r\n", $bodyPlain);
$bodyPlain = str_replace("<br>", "\r\n", $bodyPlain);
echo $bodyPlain;
?>

--PHP-alt-<?php echo $random_hash; ?>

Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?php echo nl2br($body); ?>

--PHP-alt-<?php echo $random_hash; ?>--

--PHP-mixed-<?php echo $random_hash; ?>

Content-Type: application/zip; name="<?php echo $fileName;?>"
Content-Transfer-Encoding: base64
Content-Disposition: attachment

<?php echo $attachment; ?>

--PHP-mixed-<?php echo $random_hash; ?>--

		<?php
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
		//send the email
		$mail_sent = @mail( $to, $subject, $message, $headers, "-f".MailManager::$return );

		//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed"
		return  $mail_sent ? true : false;
  }

  /**
   * Main method to send emails
   *
   * @param array $recipients
   * @param string $subject
   * @param string $body
   * @param array $attachments
   * @return bool
   */
  public static function sendEmail($recipients, $subject, $body, $attachments = false)
  {
    if (is_array($recipients))
    {
      $to = "";
      foreach ($recipients as $email)
      {
        $to .= "$email,";
      }
    }
    else
    {
      $to = "$recipients";
    }

    MailManager::$lastError = null;
    
    if (CoreConfig::MAIL_STANDARD)
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
   * Get the list of email address to send the email
   *
   * @param array $emailGroups
   * @return array
   */
  public static function getRecipients($emailGroups = array('Programmers'))
  {
  	$recipients = array();
    if (count($recipients) == 0)
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
  public static function sendWarningEmail($className, $methodName, $description){
    $body = "<b>Warning</b><br><br>";
    $body .= "<b>Class Name: </b> $className<br>";
    $body .= "<b>Method Name: </b> $methodName<br>";
    $body .= "<b>Description: </b> $description<br>";
    MailManager::sendEmail(MailManager::getRecipients(), "Warning", $body);
  }

  /**
   * Use this method to send a critical email, like db errors, or logic errors
   *
   * @param string $className
   * @param string $methodName
   * @param string $subject
   * @param string $description
   */
  public static function sendCriticalErrorEmail($subject, $description){
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
  public static function sendInfoEmail($subject, $description){
    $body = "<b>Information</b><br><br>";
    $body .= "<b>Description: </b> $description<br>";

    MailManager::sendEmail(MailManager::getRecipients(), $subject, $body);
  }
  
  /**
   * get email template and replace variables
   * 
   * @param string $name
   * @param array $params [optional]
   * @return string
   */
  public static function getEmailTemplate($name, $params = array())
  {
  	$templatePath = CoreConfig::TEMPLATE_PATH.$name.CoreConfig::TEMPLATE_FILE_EXTENSION;
  	
  	if(!Util::file_exists($templatePath))
  	{
  		$template = "";
  	}else
  	{
  	  $template = file_get_contents($templatePath,FILE_USE_INCLUDE_PATH);

	  	if(count($params) > 0)
	  	{
	  		foreach ($params as $key=>$value) 
	    	{
					$template = str_replace("{".$key."}", $value, $template);
	    	}  		
	  	}  		
  	}
  	
    return $template;
  }
  
}

?>