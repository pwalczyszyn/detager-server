<?php
require_once 'Zend/Amf/Auth/Abstract.php';
require_once 'Zend/Auth/Result.php';

require_once APPLICATION_PATH . '/models/Daos/User.php';
require_once APPLICATION_PATH . '/models/Dtos/User.php';

class Auth extends Zend_Amf_Auth_Abstract
{

	public function authenticate() 
	{
		$userTable = new Default_Dao_User();
		$stmt = $userTable->select()
			->where("username = ?", $this->_username)
			->where("password = ?", md5($this->_password))
			->where("valid is true");
		$row = $userTable->fetchRow($stmt);

		if ($row != null)
		{
			$user = new Default_Dto_User();
			$user->id = $row['id'];
			$user->username = $row['username'];
			$user->email = $row['email'];
			$user->description = $row['description'];
			$user->website = $row['website'];
			$user->entryDate = $row['entry_date'];
			
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);		
		}
		else
		{
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                null,
                array('Wrong Password')
                );    	
		}				
	}
}
