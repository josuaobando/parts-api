<?php

/**
 * @author Josua
 */
class System
{

  /**
   * TblSystem reference
   *
   * @var TblSystem
   */
  private $tblSystem;

  /**
   * Account reference
   *
   * @var Account
   */
  private $account;

  /**
   * Constructor
   *
   * @param Account $account
   */
  public function __construct($account)
  {
    $this->account = $account;
    $this->tblSystem = TblSystem::getInstance();
  }

  /**
   * get agencies
   *
   * @return array
   */
  public function getAgencies()
  {
    return $this->tblSystem->getAgencies();
  }

  /**
   * update agency
   *
   * @param int $agencyId
   * @param string $agencyName
   * @param int $agencyStatus
   *
   * @return int
   */
  public function updateAgency($agencyId, $agencyName, $agencyStatus)
  {
    return $this->tblSystem->updateAgency($agencyId, $agencyName, $agencyStatus);
  }

  /**
   * get a list of transactions status
   *
   * @return array
   */
  public function transactionStatus()
  {
    return $this->tblSystem->getTransactionStatus();
  }

  /**
   * get a list of transactions by status id
   *
   * @param int $statusId
   *
   * @return array
   */
  public function transactions($statusId)
  {
    return $this->tblSystem->getTransactions($statusId, $this->account->getAccountId());
  }

  /**
   * get a list of transactions report
   *
   * @param int $statusId
   * @param string $beginDate
   * @param string $endDate
   * @param string $controlNumber
   *
   * @return array
   */
  public function transactionsReport($statusId, $beginDate = "", $endDate = "", $controlNumber = "")
  {
    return $this->tblSystem->getTransactionsReport($statusId, $this->account->getAccountId(), $beginDate, $endDate, $controlNumber);
  }

}

?>