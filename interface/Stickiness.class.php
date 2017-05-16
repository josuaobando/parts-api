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
   * [Rejected] Already linked to a one of your receivers
   */
  const STATUS_CODE_LINKED = '3';
  /**
   * [Rejected] Already linked to other receiver with a pending transaction.
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
  private $agencyId;
  /**
   * @var person
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
   * @return int
   */
  public function getAgencyId()
  {
    return $this->agencyId;
  }

  /**
   * @param int $agencyId
   */
  public function setAgencyId($agencyId)
  {
    $this->agencyId = $agencyId;
  }

  /**
   *  restore or get stickiness data
   */
  public function create()
  {
    $this->stickinessId = $this->tblStickiness->create($this->customerId, $this->personId);
  }

  private function update()
  {
    $this->stickinessId = $this->tblStickiness->update($this->stickinessId, $this->verificationId, $this->verification);
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
        $this->customer = $stickinessData['Customer'];
        $this->customerId = $stickinessData['Customer_Id'];
        $this->person = $stickinessData['Person'];
        $this->personId = $stickinessData['Person_Id'];
        $this->personalId = $stickinessData['PersonalId'];
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
    $params['agencyId'] = ($this->agencyId == 2 || $this->agencyId == 5) ? self::AGENCY_CANAS : self::AGENCY_PAVON;
    $params['agencyId'] = 5;

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
      //prepare request
      $params_string = utf8_encode(http_build_query($this->authParams(), '', '&'));

      $wsConnector = new WS();
      $wsConnector->setReader(new Reader_Json());
      $result = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'account/', $params_string);

      return ($result && $result->code == 1);
    }
    catch(WSException $ex)
    {

    }
    return false;
  }

  /**
   * The web service checks if the sender is still available for new receiver's, is already linked to a receiver or is linked to a different merchant or company.
   */
  public function process()
  {
    if(!$this->verificationId && $this->checkConnection())
    {
      $result = null;
      try
      {
        $params = $this->authParams();
        //required param
        $params['sender'] = $this->customer;
        $params['receiver'] = $this->person;
        $params['receiverId'] = $this->personalId;
        //prepare request
        $params_string = utf8_encode(http_build_query($params, '', '&'));

        $wsConnector = new WS();
        $wsConnector->setReader(new Reader_Json());
        $result = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'check/', $params_string);
        $this->tblStickiness->addProviderMessage($this->stickinessId, $wsConnector->getLastRequest(), $result);
      }
      catch(WSException $ex)
      {

      }

      if($result)
      {
        $code = $result->code;
        switch($code)
        {
          case self::STATUS_CODE_SUCCESS:
            if($result->response && $result->response->verification)
            {
              $verification = $result->response->verification;
              $this->verificationId = $verification->id;
              $this->verification = $verification->status;
              //update stickiness
              $this->update();
            }
            break;
          case self::STATUS_CODE_FAILED:
            //error
            break;
          case self::STATUS_CODE_LINKED_PENDING:
            //do nothing
          case self::STATUS_CODE_LINKED_OTHER:
          case self::STATUS_CODE_LINKED:
            throw new InvalidStateException("The Customer has Stickiness with another agency.");
            break;
          default:
            //do nothing
        }

      }
    }
  }

  /**
   * The web service confirms or completes the transaction, in this service is where the sender gets linked to the receiver.
   */
  public function complete()
  {
    if($this->verificationId && $this->verification == self::STATUS_VERIFICATION_PENDING && $this->checkConnection())
    {
      $result = null;
      try
      {
        $params = $this->authParams();
        //required param
        $params['verificationId'] = $this->verificationId;
        $params['receiver'] = $this->person;
        $params['receiverId'] = $this->personalId;
        //prepare request
        $params_string = utf8_encode(http_build_query($params, '', '&'));

        $wsConnector = new WS();
        $wsConnector->setReader(new Reader_Json());
        $result = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'confirm/', $params_string);
        $this->tblStickiness->addProviderMessage($this->stickinessId, $wsConnector->getLastRequest(), $result);
      }
      catch(WSException $ex)
      {

      }

      if($result)
      {
        $code = $result->code;
        switch($code)
        {
          case self::STATUS_CODE_SUCCESS:
            if($result->response && $result->response->verification)
            {
              $verification = $result->response->verification;
              $this->verificationId = $verification->id;
              $this->verification = $verification->status;
              //update stickiness
              $this->update();
            }
            break;
          case self::STATUS_CODE_FAILED:
            //error
            break;
          case self::STATUS_CODE_LINKED_PENDING:
          case self::STATUS_CODE_LINKED_OTHER:
            break;
          case self::STATUS_CODE_LINKED:
            //do nothing
            break;
          default:
            //do nothing
        }

      }
    }
  }

}

?>