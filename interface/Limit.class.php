<?php

/**
 * @author Josua
 */
class Limit
{

  /**
   * min amount
   */
  const LIMIT_TYPE_MIN = 'MIN';
  /**
   * max amount
   */
  const LIMIT_TYPE_MAX = 'MAX';
  /**
   * number of transactions
   */
  const LIMIT_TYPE_COUNT = 'COUNT';

  /**
   * interval daily
   */
  const LIMIT_INTERVAL_DAILY = 'DAILY';
  /**
   * interval weekly
   */
  const LIMIT_INTERVAL_WEEKLY = 'WEEKLY';
  /**
   * interval monthly
   */
  const LIMIT_INTERVAL_MONTHLY = 'MONTHLY';

  /**
   * TblLimit reference
   *
   * @var TblLimit
   */
  private $tblLimit;

  /**
   * @var Transaction
   */
  private $transaction;

  /**
   * @var Customer
   */
  private $customer;

  /**
   * @var array
   */
  private $limitDetails;

  /**
   * @var array
   */
  private $stats = array();

  /**
   * Constructor
   *
   * @param Transaction $transaction
   * @param Customer $customer
   */
  public function __construct($transaction, $customer)
  {
    $this->customer = $customer;
    $this->transaction = $transaction;
    $this->tblLimit = TblLimit::getInstance();
    $this->limitDetails = $this->tblLimit->getLimitDetails($this->customer->getAgencyTypeId());
  }

  /**
   * Limits evaluation
   *
   * @throws InvalidStateException
   */
  public function evaluate()
  {
    // check blacklisted
    $isBlacklisted = $this->customer->isBlacklisted();
    if($isBlacklisted > 0)
    {
      $agencyType = $this->transaction->getAgencyTypeId();
      if($agencyType == Transaction::AGENCY_MONEY_GRAM)
      {
        throw new InvalidStateException("The Customer has been blacklisted by MG International. Suggest RIA option.");
      }
      elseif($agencyType == Transaction::AGENCY_WESTERN_UNION)
      {
        throw new InvalidStateException("The Customer has been blacklisted by WU International. Suggest RIA option.");
      }
      elseif($agencyType == Transaction::AGENCY_RIA)
      {
        throw new InvalidStateException("The Customer has been blacklisted by RIA International");
      }
    }

    //load customer stats
    $transactionTypeId = $this->transaction->getTransactionTypeId();
    $this->stats = $this->customer->getStats($transactionTypeId);

    foreach($this->limitDetails as $limit)
    {
      $limitTransactionType = $limit['TransactionType_Id'];
      if($limitTransactionType == 0 || $limitTransactionType == $transactionTypeId)
      {
        $limitScope = $limit['LimitScope'];
        $function = "check$limitScope";
        $this->$function($limit);
      }
    }
  }

  /**
   * Evaluate the customer limits
   *
   * @param array $limit
   *
   * @throws InvalidStateException
   */
  private function checkCustomer($limit)
  {
    $limitValue = $limit['Value'];
    $limitInterval = strtoupper($limit['LimitInterval']);
    $limitType = strtoupper($limit['LimitType']);
    switch($limitType)
    {
      case self::LIMIT_TYPE_MIN:
        //do nothing
        break;
      case self::LIMIT_TYPE_MAX:
        //do nothing
        break;
      case self::LIMIT_TYPE_COUNT:

        switch($limitInterval)
        {
          case self::LIMIT_INTERVAL_DAILY:
            $dailyTransactions = $this->stats['DailyTransactions'];
            if($dailyTransactions && $dailyTransactions >= $limitValue)
            {
              throw new InvalidStateException("Limits: The Customer has exceeded the Daily limit of ".$this->stats['TransactionTypeName']."s");
            }
            break;
          case self::LIMIT_INTERVAL_WEEKLY:
            $weeklyTransactions = $this->stats['WeeklyTransactions'];
            if($weeklyTransactions && $weeklyTransactions >= $limitValue)
            {
              throw new InvalidStateException("Limits: The Customer has exceeded the Weekly limit of ".$this->stats['TransactionTypeName']."s");
            }
            break;
          case self::LIMIT_INTERVAL_MONTHLY:
            $monthlyTransactions = $this->stats['MonthlyTransactions'];
            if($monthlyTransactions && $monthlyTransactions >= $limitValue)
            {
              throw new InvalidStateException("Limits: The Customer has exceeded the Monthly limit of ".$this->stats['TransactionTypeName']."s");
            }
            break;
        }
        break;
    }
  }

  /**
   * Evaluate the transaction limits
   *
   * @param array $limit
   *
   * @throws InvalidStateException
   */
  private function checkTransaction($limit)
  {
    $limitValue = $limit['Value'];
    $limitType = strtoupper($limit['LimitType']);
    switch($limitType)
    {
      case self::LIMIT_TYPE_MIN:
        if($limitValue > $this->transaction->getAmount())
        {
          throw new InvalidStateException("Limits: The minimum allowable amount is: ".$limit['Value']." USD");
        }
        break;
      case self::LIMIT_TYPE_MAX:
        if($limitValue < $this->transaction->getAmount())
        {
          throw new InvalidStateException("Limits: The maximum allowable amount is: ".$limit['Value']." USD");
        }
        break;
      case self::LIMIT_TYPE_COUNT:
        //do nothing
        break;
    }
  }

  /**
   * Evaluate the person limits
   *
   * @param array $limit
   *
   * @throws InvalidStateException
   */
  private function checkPerson($limit)
  {
    // Do nothing
  }

}

?>