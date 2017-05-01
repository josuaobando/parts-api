<?php

/**
 * Class Session
 */
class Session
{

  const SID_ACCOUNT = 'account';
  const SID_COUNTRIES = 'countries';

  /**
   * get account from session
   *
   * @param null $username
   *
   * @return Account
   */
  public static function getAccount($username = null)
  {
    $account = new Account();
    $accountSession = $_SESSION[self::SID_ACCOUNT];
    if($accountSession && $accountSession instanceof Account)
    {
      $account = $accountSession;
    }
    elseif($username)
    {
      $account = new Account($username);
      $_SESSION[self::SID_ACCOUNT] = $account;
    }

    return $account;
  }

  /**
   * get countries
   *
   * @return array
   */
  public static function getCountries()
  {
    $countriesSession = $_SESSION[self::SID_COUNTRIES];
    if(!$countriesSession)
    {
      $tblCountry = TblCountry::getInstance();
      $countries = $tblCountry->getCountries();
      $_SESSION[self::SID_COUNTRIES] = $countries;
    }
    else
    {
      $countries = $countriesSession;
    }

    return $countries;
  }

}

?>