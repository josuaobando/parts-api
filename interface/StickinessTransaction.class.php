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

}

?>