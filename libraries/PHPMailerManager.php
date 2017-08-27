<?php

/** PHPMailer root directory */
if(!defined('PHPMailer_ROOT')){
  define('PHPMailer_ROOT', dirname(__FILE__).'/');
  require(PHPMailer_ROOT.'PHPMailer/PHPMailerAutoload.php');
}

class PHPMailerManager extends PHPMailer
{
  public function __construct($exceptions = null)
  {
    parent::__construct($exceptions);
  }
}
