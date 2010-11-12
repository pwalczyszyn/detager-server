<?php
abstract class AbstractService
{

	protected function getIdentity()
	{
		return Zend_Auth::getInstance()->getIdentity();
	}
	
	protected function getIdentityId()
	{
		$result = null;
		$user = Zend_Auth::getInstance()->getIdentity();
		if ($user != null)
			$result = $user->id;
		return $result;
	}
	
	protected function getIdentityRow()
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$userTable = new Default_Dao_User();
		return $userTable->fetchRow("id = '$user->id'");
	}
	
}