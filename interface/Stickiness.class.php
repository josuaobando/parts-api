<?php

class Stickiness
{

  /**
   * @var int
   */
  private $stickinessId;
  /**
   * @var int
   */
  private $customerId;
  /**
   * @var int
   */
  private $personId;
  /**
   * @var int
   */
  private $isActive;

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
  public function getIsActive()
  {
    return $this->isActive;
  }

  /**
   * @param int $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
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
        $this->customerId = $stickinessData['Customer_Id'];
        $this->personId = $stickinessData['Person_Id'];
        $this->isActive = $stickinessData['IsActive'];
      }
    }
  }

}

?>