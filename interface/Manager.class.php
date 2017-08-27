<?php

/**
 * @author Josua
 */
class Manager
{

  /**
   * Account reference
   *
   * @var Account
   */
  private $account;

  /**
   * TblManager reference
   *
   * @var TblManager
   */
  private $tblManager;

  /**
   * new Manager instance
   *
   * @param Account $account
   */
  public function __construct($account)
  {
    $this->account = $account;
    $this->tblManager = TblManager::getInstance();
  }

}

?>