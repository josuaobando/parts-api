<?php

/**
 * @author Josua
 */
class Customer
{

  private $customerId;
  private $agencyId;
  private $agencyTypeId;
  private $firstName;
  private $lastName;
  private $country;
  private $countryId;
  private $countryName;
  private $state;
  private $stateId;
  private $stateName;
  private $phone;

  /**
   * TblCustomer reference
   *
   * @var TblCustomer
   */
  private $tblCustomer;

  /**
   * TblUtil reference
   *
   * @var TblUtil
   */
  private $tblUtil;

  /**
   * new instance of person
   */
  public function __construct()
  {
    $this->tblCustomer = TblCustomer::getInstance();
    $this->tblUtil = TblUtil::getInstance();
  }

  /**
   * load object using the request
   *
   * @param Account $account
   * @param WSRequest $wsRequest
   *
   * @throws InvalidParameterException
   * @throws InvalidStateException
   */
  public function validateFromRequest($account, $wsRequest)
  {
    $this->agencyTypeId = $wsRequest->requireNumericAndPositive('type');
    $this->firstName = trim($wsRequest->requireNotNullOrEmpty('first_name'));
    $this->lastName = trim($wsRequest->requireNotNullOrEmpty('last_name'));
    $this->country = trim($wsRequest->requireNotNullOrEmpty('country'));
    $this->state = trim($wsRequest->requireNotNullOrEmpty('state'));
    $this->phone = trim($wsRequest->requireNotNullOrEmpty('phone'));

    $countryData = $this->tblUtil->getCountry($this->country);
    if(!$countryData)
    {
      throw new InvalidParameterException('country', $this->country, 'CountryCode');
    }
    $this->countryId = $countryData['Country_Id'];
    $this->countryName = $countryData['Name'];

    $stateData = $this->tblUtil->getState($this->countryId, $this->state);
    if(!$stateData)
    {
      throw new InvalidParameterException('state', $this->state, 'StateCode');
    }
    $this->stateId = $stateData['CountryState_Id'];
    $this->stateName = $stateData['Name'];

    $this->validate($account->getCompanyId(), $account->getAccountId());
  }

  /**
   * validate the information and update or create the customer
   *
   * @param int $companyId
   * @param int $accountId
   *
   * @throws InvalidStateException
   */
  private function validate($companyId, $accountId)
  {
    //customer data
    $customerData = null;

    //validate if exist a similar customer
    $similarList = $this->tblCustomer->getSimilar($companyId, $this->agencyTypeId, $this->firstName, $this->lastName);
    if($similarList && COUNT($similarList) > 0){
      $customerName = $this->getCustomer();
      foreach($similarList as $similar){
        $percent = 0;
        $registerCustomerName = $similar['CustomerName'];
        similar_text($customerName, $registerCustomerName, $percent);
        if($percent >= 90){
          $this->customerId = $similar['CustomerId'];
          $this->agencyId = $similar['AgencyId'];
          Log::custom('Similar', "Request: $customerName Register: $registerCustomerName");
          break;
        }
      }
    }

    //if not have register, check customer from request
    if(!$this->customerId){
      $customerData = $this->tblCustomer->validate($companyId, $accountId, $this->agencyTypeId, $this->firstName, $this->lastName, $this->countryId, $this->stateId, $this->phone);
      $this->customerId = $customerData['CustomerId'];
      $this->agencyId = $customerData['AgencyId'];
    }

    if(!$this->customerId){
      throw new InvalidStateException("invalid customer information");
    }

    if(!$this->agencyId){
      throw new InvalidStateException("The agency is not available");
    }
  }

  /**
   * Validate if customer [firstname + lastname] is blocked by the Network
   *
   * @return int if is upper to zero, is blacklisted
   */
  public function isBlacklisted()
  {
    return $this->tblCustomer->getIsBlacklisted($this->customerId);
  }

  /**
   * @return int
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }

  /**
   * @return int
   */
  public function getAgencyId()
  {
    return $this->agencyId;
  }

  /**
   * @return int
   */
  public function getAgencyTypeId()
  {
    return $this->agencyTypeId;
  }

  /**
   * get the customer
   *
   * @return string
   */
  public function getCustomer()
  {
    return $this->firstName." ".$this->lastName;
  }

  /**
   * get the from representation
   *
   * @return string
   */
  public function getFrom()
  {
    return $this->countryName.", ".$this->stateName;
  }

  /**
   * serialize object
   *
   * @return array
   */
  public function toArray()
  {
    $data = array();

    $data['first_name'] = $this->firstName;
    $data['last_name'] = $this->lastName;
    $data['country'] = $this->countryName;
    $data['state'] = $this->stateName;

    return $data;
  }

  /**
   * get customer stats
   *
   * @param int $transactionTypeId
   *
   * @see Transaction::TYPE_RECEIVER, Transaction::TYPE_SENDER
   *
   * @return array
   */
  public function getStats($transactionTypeId = 0)
  {
    return $this->tblCustomer->getStats($this->customerId, $transactionTypeId);
  }

}

?>