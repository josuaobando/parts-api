<?php

/**
 * @author Josua
 */
class StickinessTransaction
{

  /**
   * @var int
   */
  private $stickinessTransactionId;

  /**
   * @var int
   */
  private $stickinessId;

  /**
   * @var int
   */
  private $transactionId;

  /**
   * @var int
   */
  private $verificationId;

  /**
   * @var string
   */
  private $verification;

  /**
   * TblStickinessTransaction reference
   *
   * @var TblStickinessTransaction
   */
  private $tblStickinessTransaction;

  /**
   * new TblStickinessTransaction instance
   */
  public function __construct()
  {
    $this->tblStickinessTransaction = TblStickinessTransaction::getInstance();
  }

  /**
   *  restore or get stickiness data
   */
  public function restore()
  {
    if($this->transactionId)
    {
      $stickinessTransactionData = $this->tblStickinessTransaction->get($this->transactionId);
      if($stickinessTransactionData)
      {
        $this->stickinessTransactionId = $stickinessTransactionData['StickinessTransaction_Id'];
        $this->stickinessId = $stickinessTransactionData['Stickiness_Id'];
        $this->transactionId = $stickinessTransactionData['Transaction_Id'];
        $this->verificationId = $stickinessTransactionData['Verification_Id'];
        $this->verification = $stickinessTransactionData['Verification'];
      }
    }
  }

  public function add()
  {
    $this->stickinessTransactionId = $this->tblStickinessTransaction->insert($this->stickinessId, $this->transactionId, $this->verificationId, $this->verification);
  }

  public function update()
  {
    $this->tblStickinessTransaction->update($this->stickinessTransactionId, $this->verificationId, $this->verification);
  }

  /**
   * @return int
   */
  public function getStickinessTransactionId()
  {
    return $this->stickinessTransactionId;
  }

  /**
   * @param int $stickinessTransactionId
   */
  public function setStickinessTransactionId($stickinessTransactionId)
  {
    $this->stickinessTransactionId = $stickinessTransactionId;
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
  public function getTransactionId()
  {
    return $this->transactionId;
  }

  /**
   * @param int $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }

  /**
   * @return int
   */
  public function getVerificationId()
  {
    return $this->verificationId;
  }

  /**
   * @param int $verificationId
   */
  public function setVerificationId($verificationId)
  {
    $this->verificationId = $verificationId;
  }

  /**
   * @return string
   */
  public function getVerification()
  {
    return $this->verification;
  }

  /**
   * @param string $verification
   */
  public function setVerification($verification)
  {
    $this->verification = $verification;
  }

}

?>