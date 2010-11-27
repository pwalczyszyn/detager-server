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

	public function activateTwitter($queryData)
	{
		$loggedInUserId = $this->getIdentityId();
		if ($loggedInUserId)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$userDao = new Default_Dao_User;
			$userRow = $userDao->fetchRow($db->quoteInto('id = ?', $loggedInUserId));
			
			if ($userRow)
			{
		        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/twitter.ini');
		        $consumer = new Zend_Oauth_Consumer($config);

		        $get = array();
		        parse_str($queryData, $get);
		    	$token = $consumer->getAccessToken($get, unserialize($userRow['twitter_request_token']));
    	
				$db = Zend_Db_Table::getDefaultAdapter();
				$data = array('twitter_access_token' => serialize($token), 'twitter_request_token' => NULL);
				$userDao->update($data, $db->quoteInto('id = ?', $loggedInUserId));
		    }
		}
	}
	
	public function signOut()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		session_destroy();
	}
}