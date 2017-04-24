<?php

/**
 * Class Session
 */
class Session
{

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
    $accountSession = $_SESSION['account'];
    if($accountSession && $accountSession instanceof Account)
    {
      $account = $accountSession;
    }
    elseif($username)
    {
      $account = new Account($username);
      $_SESSION['account'] = $account;
    }

    return $account;
  }

  /**
   * @return array
   */
  public static function getCountries()
  {
    $countriesSession = $_SESSION['countries'];
    if(!$countriesSession)
    {
      $tblCountry = TblCountry::getInstance();
      $countries = $tblCountry->getCountries();
      $_SESSION['countries'] = $countries;
    }
    else
    {
      $countries = $countriesSession;
    }

    return $countries;
  }

}

?>