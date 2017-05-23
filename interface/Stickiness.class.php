<?php

class Stickiness
{

  /**
   * Action was successfully executed with not errors.
   */
  const STATUS_CODE_SUCCESS = '1';
  /**
   * There was an issue with the authentication process or there was an invalid state in the execution.
   */
  const STATUS_CODE_FAILED = '2';
  /**
   * Already linked to a one of your receivers
   */
  const STATUS_CODE_LINKED = '3';
  /**
   * Already linked to a one of your pending receivers
   */
  const STATUS_CODE_LINKED_PENDING = '4';
  /**
   * [Rejected] Already linked to other receiver
   */
  const STATUS_CODE_LINKED_OTHER = '5';

  /**
   * Pending Stickiness
   */
  const STATUS_VERIFICATION_PENDING = 'pending';
  /**
   * Approved Stickiness
   */
  const STATUS_VERIFICATION_APPROVED = 'approved';

  /**
   * Rejected Stickiness
   */
  const STATUS_VERIFICATION_REJECTED = 'rejected';

  const AGENCY_CANAS = '13';
  const AGENCY_PAVON = '14';

  /**
   * @var int
   */
  private $stickinessId;
  /**
   * @var int
   */
  private $verificationId;
  /**
   * @var string
   */
  private $verification;
  /**
   * @var string
   */
  private $customer;
  /**
   * @var int
   */
  private $customerId;
  /**
   * @var int
   */
  private $agencyP2P;
  /**
   * @var string
   */
  private $person;
  /**
   * @var int
   */
  private $personId;
  /**
   * @var int
   */
  private $personalId;

  /**
   * TblStickiness reference
   *
   * @var TblStickiness
   */
  private $tblStickiness;

  /**
   * new instance
   */
  public function __construct()
  {
    $this->tblStickiness = TblStickiness::getInstance();
  }

  /**
   * @return int
   */
  public function getStickinessId()
  {
    return $this->stickinessId;
  }

  /**
   * @param int $stickinessId
   */
  public function setStickinessId($stickinessId)
  {
    $this->stickinessId = $stickinessId;
  }

  /**
   * @return int
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }

  /**
   * @param int $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }

  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }

  /**
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }

  /**
   * @return int
   */
  public function getPersonId()
  {
    return $this->personId;
  }

  /**
   * @param int $personId
   */
  public function setPersonId($personId)
  {
    $this->personId = $personId;
  }

  /**
   * @return int
   */
  public function getPersonalId()
  {
    return $this->personalId;
  }

  /**
   * @param int $personalId
   */
  public function setPersonalId($personalId)
  {
    $this->personalId = $personalId;
  }

  /**
   * @return string
   */
  public function getPerson()
  {
    return $this->person;
  }

  /**
   * @param string
   */
  public function setPerson($person)
  {
    $this->person = $person;
  }

  /**
   *  create new stickiness
   */
  public function create()
  {
    $this->stickinessId = $this->tblStickiness->create($this->customerId, $this->personId, $this->verificationId, $this->verification);
  }

  /**
   * update data
   */
  private function update()
  {
    $this->tblStickiness->update($this->stickinessId, $this->verificationId, $this->verification);
  }

  /**
   *  restore or get stickiness data
   */
  public function restore()
  {
    if($this->customerId)
    {
      $stickinessData = $this->tblStickiness->getByCustomerId($this->customerId);
      if($stickinessData)
      {
        $this->stickinessId = $stickinessData['Stickiness_Id'];
        $this->verificationId = $stickinessData['Verification_Id'];
        $this->verification = $stickinessData['Verification'];

        if(!$this->customer)
        {
          $this->customerId = $stickinessData['Customer_Id'];
        }
        $this->customer = $stickinessData['Customer'];

        if(!$this->person)
        {
          $this->personId = $stickinessData['Person_Id'];
        }
        $this->person = $stickinessData['Person'];
        $this->personalId = $stickinessData['PersonalId'];

        $this->agencyP2P = $stickinessData['AgencyP2P'];
      }
    }
  }

  //---------------------------------------------------
  //--External connection to validate Person 2 Person--
  //---------------------------------------------------

  /**
   * verification with provider
   */
  public function verify()
  {
    if(CoreConfig::WS_STICKINESS_ACTIVE)
    {
      if($this->verificationId && $this->verification == self::STATUS_VERIFICATION_PENDING)
      {
        $this->complete();
      }
      elseif(!$this->verificationId)
      {
        $this->register();
      }
    }
  }

  /**
   * authentication params
   *
   * @return array
   */
  private function authParams()
  {
    $params = array();
    $params['format'] = 'json';
    $params['companyId'] = CoreConfig::WS_STICKINESS_CREDENTIAL_COMPANY;
    $params['password'] = CoreConfig::WS_STICKINESS_CREDENTIAL_PASSWORD;
    $params['key'] = CoreConfig::WS_STICKINESS_CREDENTIAL_KEY;
    $params['agencyId'] = $this->agencyP2P;

    return $params;
  }

  /**
   * check credentials
   *
   * @return bool
   */
  private function checkConnection()
  {
    try
    {
      $params = $this->authParams();

      $wsConnector = new WS();
      $wsConnector->setReader(new Reader_Json());
      $result = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'account/', $params);

      return ($result && $result->code == 1);
    }
    catch(WSException $ex)
    {
      ExceptionManager::handleException($ex);
    }

    return false;
  }

  /**
   * The web service checks if the sender is still available for new receiver's, is already linked to a receiver or is linked to a different merchant or company.
   */
  private function register()
  {
    if($this->checkConnection())
    {
      $result = null;
      try
      {
        $params = $this->authParams();
        //required param
        $params['sender'] = $this->customer;
        $params['receiver'] = $this->person;
        $params['receiverId'] = $this->personalId;

        $wsConnector = new WS();
        $wsConnector->setReader(new Reader_Json());
        $result = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'check/', $params);

        $this->tblStickiness->addProviderMessage($this->stickinessId, $wsConnector->getLastRequest(), $result);
      }
      catch(Exception $ex)
      {
        ExceptionManager::handleException($ex);
      }

      if($result)
      {
        if($result->code == self::STATUS_CODE_SUCCESS)
        {
          if($result->response && $result->response->verification)
          {
            $verification = $result->response->verification;
            if($verification->status == self::STATUS_VERIFICATION_PENDING)
            {
              $this->verificationId = $verification->id;
              $this->verification = $verification->status;
              $this->create();
            }
          }
        }
        elseif($result->code == self::STATUS_CODE_LINKED_PENDING)
        {
          throw new InvalidStateException("Due to restrictions, we can not perform the transaction.");
        }
        elseif($result->code == self::STATUS_CODE_LINKED_OTHER)
        {
          throw new InvalidStateException("The Customer has Stickiness with another Person.");
        }
      }
    }
  }

  /**
   * The web service confirms or completes the transaction, in this service is where the sender gets linked to the receiver.
   */
  private function complete()
  {
    if($this->checkConnection())
    {
      $result = null;
      try
      {
        $params = $this->authParams();
        //required param
        $params['verificationId'] = $this->verificationId;
        $params['receiver'] = $this->person;
        $params['receiverId'] = $this->personalId;

        $wsConnector = new WS();
        $wsConnector->setReader(new Reader_Json());
        $result = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'confirm/', $params);

        $this->tblStickiness->addProviderMessage($this->stickinessId, $wsConnector->getLastRequest(), $result);
      }
      catch(Exception $ex)
      {
        ExceptionManager::handleException($ex);
      }

      if($result)
      {
        if($result->code == self::STATUS_CODE_SUCCESS)
        {
          if($result->response && $result->response->verification)
          {
            $verification = $result->response->verification;
            if($verification->status == self::STATUS_VERIFICATION_APPROVED)
            {
              $this->verification = $verification->status;
              $this->update();
            }
          }
        }
        elseif($result->code == self::STATUS_CODE_LINKED_OTHER)
        {
          if($result->response && $result->response->verification)
          {
            $verification = $result->response->verification;
            if($verification->status == self::STATUS_VERIFICATION_APPROVED)
            {
              $this->verification = $verification->status;
              $this->update();
            }
          }
        }
      }

    }
  }

}

?>