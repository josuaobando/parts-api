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

}

?>