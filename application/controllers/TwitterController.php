<?php

class TwitterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	if ($this->getRequest()->isGet())
    	{
    		$uid = $this->_request->getParam('uid', null);
    		if ($uid)
    		{
		        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/twitter.ini');
		        $consumer = new Zend_Oauth_Consumer($config);
		        
				// fetch a request token
				$token = $consumer->getRequestToken();
				 
				$db = Zend_Db_Table::getDefaultAdapter();
				$entity = new Default_Dao_User;
				$data = array('twitter_request_token' => serialize($token));
				$entity->update($data, $db->quoteInto('id = ?', $uid));
				
				// redirect the user
				$consumer->redirect();
		    			
    		}
    	}
    }

    public function callbackAction()
    {
// http://detager.com.localhost/twitter/callback?oauth_token=SVUCV5bCIGYrwshH8kZm45drLYriCY13nuVxlO8RY&oauth_verifier=8JI7ybTTPrE93duTj8F5Lfn5y2DhzwMyihHjLYALmE
    }


}



