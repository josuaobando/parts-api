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
   * Constructor
   */
  public function __construct()
  {
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
   * @param int $accountId
   *
   * @return array
   */
  public function transactions($statusId, $accountId)
  {
    return $this->tblSystem->getTransactions($statusId, $accountId);
  }

  /**
   * get a list of transactions report
   *
   * @param int $statusId
   * @param int $transactionTypeId
   * @param int $filterAgencyType
   * @param int $accountId
   * @param string $beginDate
   * @param string $endDate
   * @param string $controlNumber
   * @param string $customer
   * @param int $pageStart
   * @param int $pageSize
   *
   * @return array
   */
  public function transactionsReport($statusId, $transactionTypeId, $filterAgencyType, $accountId, $beginDate, $endDate, $controlNumber, $customer, $pageStart, $pageSize)
  {
    return $this->tblSystem->getTransactionsReport($statusId, $transactionTypeId, $filterAgencyType, $accountId, $beginDate, $endDate, $controlNumber, $customer, $pageStart, $pageSize);
  }

}

?>