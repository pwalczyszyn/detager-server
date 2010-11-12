<?php

class ServicesController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}

    public function amfAction()
    {
		$server = new Zend_Amf_Server();
		
		require_once APPLICATION_PATH . '/auth/Auth.php';
		
		$server->setAuth(new Auth());		
		$server->addDirectory(APPLICATION_PATH . '/services/');

        $response = $server->handle();
        echo ($response);
    }

}

