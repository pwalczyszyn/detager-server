<?php
require_once APPLICATION_PATH . '/services/AbstractService.php';

class UsersService extends AbstractService
{ 
	
	public function signIn()
	{	
		error_log(var_export('login', true), 0);
		
		$result = new Default_Dto_SignInResult();
		$user = Zend_Auth::getInstance()->getIdentity();
		
		if ($user != null)
		{
			$user->entryDate = new DateTime($user->entryDate);
			
			$result->signedIn = true;
			$result->user = $user;
		}
		else
		{
			$result->signedIn = false;
		}
		return $result;
	}

	public function signOut()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		session_destroy();
	}
}