<?php

/**
 * @author Josua
 */
class TblStickinessTransaction extends Db
{
	
	/**
	 * singleton reference for TblStickinessTransaction
	 * 
	 * @var TblStickinessTransaction
	 */
	private static $singleton = null;
	
	/**
	 * get a singleton instance of TblStickinessTransaction
	 * 
	 * @return TblStickinessTransaction
	 */
	public static function getInstance()
	{
		if (is_null(self::$singleton))
		{
			self::$singleton = new TblStickinessTransaction();
		}
		return self::$singleton;
	}

  /**
   * insert a new stickiness transaction
   *
   * @param $stickinessId
   * @param $transactionId
   * @param $verificationId
   * @param $verification
   *
   * @return int
   */
	public function insert($stickinessId, $transactionId, $verificationId, $verification)
	{
		$sql = "CALL spStickinessTransaction_Add('{stickinessId}', '{transactionId}', '{verificationId}', '{verification}', @stickinessTransactionId)";

		$params = array();
		$params['stickinessId'] = $stickinessId;
		$params['transactionId'] = $transactionId;
		$params['verificationId'] = $verificationId;
		$params['verification'] = $verification;

		$this->setOutputParams(array('stickinessTransactionId'));
		$this->executeUpdate($sql, $params);
		$output = $this->getOutputResults();
		$stickinessTransactionId = $output['stickinessTransactionId'];
		
		return $stickinessTransactionId;
	}

  /**
   * update stickiness transaction
   *
   * @param $stickinessTransactionId
   * @param $verificationId
   * @param $verification
   *
   * @return int
   */
	public function update($stickinessTransactionId, $verificationId, $verification)
	{
		$sql = "CALL spStickinessTransaction_Update('{stickinessTransactionId}', '{verificationId}', '{verification}')";
	
		$params = array();
		$params['stickinessTransactionId'] = $stickinessTransactionId;
    $params['verificationId'] = $verificationId;
		$params['verification'] = $verification;

		return $this->executeUpdate($sql, $params);
	}
	
	/**
	 * get stickiness transaction
	 * 
	 * @param int $transactionId
	 * 
	 * @return array
	 */
	public function get($transactionId)
	{
		$sql = "CALL spStickinessTransaction('{transactionId}')";
	
		$params = array();
		$params['transactionId'] = $transactionId;
	
		$row = array();
		$this->executeSingleQuery($sql, $row, $params);
	
		return $row;
	}
	
}
?>