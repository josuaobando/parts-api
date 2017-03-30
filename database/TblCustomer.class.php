<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class TblCustomer extends Db 
{
	
	/**
	 * singleton reference for TblCustomer
	 * 
	 * @var TblCustomer
	 */
	private static $singleton = null;
	
	/**
	 * get a singleton instance of TblCustomer
	 * 
	 * @return TblCustomer
	 */
	public static function getInstance()
	{
		if (is_null(self::$singleton))
		{
			self::$singleton = new TblCustomer();
		}
		return self::$singleton;
	}
	
	/**
	 * get customer data
	 * 
	 * @param string $customerId
	 * 
	 * @return array
	 */
	public function getCustomer($customerId)
	{
		$sql = "CALL customer('{customerId}')";
		
		$params = array();
		$params['customerId'] = $customerId;
		
		$row = array();
		$this->executeSingleQuery($sql, $row, $params);
				
		return $row;
	}
	
  /**
   * validate customer information
   * 
   * @param int $companyId
   * @param int $accountId
   * @param int $agencyTypeId
   * @param string $firstName
   * @param string $lastName
   * @param int $countryId
   * @param int $countryStateId
   * @param string $phone
   * 
   * @return array [CustomerId, AgencyId]
  */
	public function validate($companyId, $accountId, $agencyTypeId, $firstName, $lastName, $countryId, $countryStateId, $phone)
	{
		$sql = "CALL customer_validate('{companyId}', '{accountId}', '{agencyTypeId}', '{firstName}', '{lastName}', '{countryId}', '{countryStateId}', '{phone}', @CustomerId, @AgencyId)";
		
		$params = array();
		$params['companyId'] = $companyId;
    $params['accountId'] = $accountId;
		$params['agencyTypeId'] = $agencyTypeId;
		$params['firstName'] = $firstName;
		$params['lastName'] = $lastName;
		$params['countryId'] = $countryId;
		$params['countryStateId'] = $countryStateId;
		$params['phone'] = $phone;
		
		$this->setOutputParams(array('CustomerId', 'AgencyId'));
		$this->executeUpdate($sql, $params);
		$output = $this->getOutputResults();

		return $output;
	}
	
	/**
	 * Validate if customer [firstname + lastname] is blocked by the Network
	 *
	 * @param int $customerId
	 *
	 * @return int
	 */
	public function getIsBlacklisted($customerId)
	{
	  $sql = "CALL customer_isBlacklisted('{customerId}', @isBlocked)";
	
	  $params = array();
	  $params['customerId'] = $customerId;
	   
	  $this->setOutputParams(array('isBlocked'));
	  $this->executeUpdate($sql, $params);
	  $result = $this->getOutputResults();
	
	  return $result['isBlocked'];
	}
	
	/**
	 * get stats
	 *
	 * @param int $customerId
   * @param int $transactionTypeId
	 *
	 * @return array
	 */
	public function getStats($customerId, $transactionTypeId)
	{
	  $sql = "CALL customer_getStats('{customerId}', '{transactionTypeId}')";
	
		$params = array();
    $params['customerId'] = $customerId;
    $params['transactionTypeId'] = $transactionTypeId;
	
	  $rows = array();
	  $this->executeSingleQuery($sql, $rows, $params);
	
	  return $rows;
	}
	
}
?>