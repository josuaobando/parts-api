<?php

class Stickiness
{

  /**
   * [Pending] Action was successfully executed with not errors.
   */
  const STATUS_PENDING = '1';
  /**
   * [Rejected] There was an issue with the authentication process or there was an invalid state in the execution.
   */
  const STATUS_FAILED = '2';
  /**
   * [Pending] Already linked to a one of your receivers
   */
  const STATUS_LINKED = '3';
  /**
   * [Pending] Already linked to other receiver with a pending transaction.
   */
  const STATUS_LINKED_PENDING = '4';
  /**
   * [Rejected] Already linked to other receiver
   */
  const STATUS_LINKED_OTHER = '5';

  /**
   * @var int
   */
  private $stickinessId;
  /**
   * @var int
   */
  private $externalId;
  /**
   * @var string
   */
  private $customer;
  /**
   * @var int
   */
  private $customerId;
  /**
   * @var person
   */
  private $person;
  /**
   * @var int
   */
  private $personId;

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
   *  restore or get stickiness data
   */
  public function create()
  {
    $this->stickinessId = $this->tblStickiness->create($this->customerId, $this->personId);
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
        $this->externalId = $stickinessData['External_Id'];
        $this->customer = $stickinessData['Customer'];
        $this->customerId = $stickinessData['Customer_Id'];
        $this->person = $stickinessData['Person'];
        $this->personId = $stickinessData['Person_Id'];
      }
    }
  }

  /**
   * The web service checks if the sender is still available for new receiver's, is already linked to a receiver or is linked to a different merchant or company.
   */
  public function process()
  {
    $params = array();
    //authentication params
    $params['format'] = 'json';
    $params['companyId'] = CoreConfig::WS_STICKINESS_CREDENTIAL_COMPANY;
    $params['agencyId'] = CoreConfig::WS_STICKINESS_CREDENTIAL_AGENCY;
    $params['Password'] = CoreConfig::WS_STICKINESS_CREDENTIAL_PASSWORD;
    $params['key'] = CoreConfig::WS_STICKINESS_CREDENTIAL_KEY;
    //required param
    $params['sender'] = $this->customer;

    $wsConnector = new WS();
    $wsConnector->setReader(new Reader_Json());
    $response = $wsConnector->execPost(CoreConfig::WS_STICKINESS.'check/', $params);

    //add log
    $this->tblStickiness->addProviderMessage($this->stickinessId, $wsConnector->getLastRequest(), $response);

    if($response)
    {
      $code = $response->code;
      switch($code)
      {
        case self::STATUS_PENDING:
        case self::STATUS_LINKED:
        case self::STATUS_FAILED:
          break;
        case self::STATUS_LINKED_PENDING:
          break;
        case self::STATUS_LINKED_OTHER:
          break;
        default:
          //do nothing
      }

    }

  }

  /**
   * The web service confirms or completes the transaction, in this service is where the sender gets linked to the receiver.
   */
  private function complete()
  {

  }

}

?>