<?php

/**
 * jobando
 */
class TblStickiness extends Db
{

  /**
   * singleton reference for TblStickiness
   *
   * @var TblStickiness
   */
  private static $singleton = null;

  /**
   * get a singleton instance of TblStickiness
   *
   * @return TblStickiness
   */
  public static function getInstance()
  {
    if(is_null(self::$singleton))
    {
      self::$singleton = new TblStickiness();
    }

    return self::$singleton;
  }

  /**
   * add new stickiness
   *
   * @param int $customerId
   * @param int $personId
   *
   * @return int
   */
  public function create($customerId, $personId)
  {
    $sql = "CALL spStickiness_Add('{customerId}', '{personId}', @stickinessId)";

    $params = array();
    $params['customerId'] = $customerId;
    $params['personId'] = $personId;

    $this->setOutputParams(array('stickinessId'));
    $this->executeUpdate($sql, $params);
    $output = $this->getOutputResults();
    $stickinessId = $output['stickinessId'];

    return $stickinessId;
  }

  /**
   * get stickiness data by Customer Id
   *
   * @param int $customerId
   *
   * @return array
   */
  public function getByCustomerId($customerId)
  {
    $sql = "CALL spStickiness_ByCustomerId('{customerId}')";

    $params = array();
    $params['customerId'] = $customerId;

    $row = array();
    $this->executeSingleQuery($sql, $row, $params);

    return $row;
  }

}

?>