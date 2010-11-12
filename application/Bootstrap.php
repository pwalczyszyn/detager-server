<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Bootstrap autoloader for application resources
     * 
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => dirname(__FILE__),
        ));
        
        $autoloader->addResourceType('dao', 'models/Daos/', 'Dao')
        	->addResourceType('dto', 'models/Dtos/', 'Dto')
        	->addResourceType('helper', 'helpers/', 'Helper');
        
        return $autoloader;
    }
	
    /**
     * Bootstrap the view doctype
     * 
     * @return void
     */
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    protected function _initConfig()
	{
	    Zend_Registry::set('config', $this->getOptions());
	    Zend_Registry::set('SQL_DATE', 'YYYY-MM-dd HH:mm:ss');
	}
    
	protected function _initEmailTransport()
	{
        $aConfig    = $this->getOptions();
        $emailConfig    = array(
                        'auth'=>'login',
                        'username'=>$aConfig['email']['username'],
                        'password'=>$aConfig['email']['password'],
//                        'ssl'=>$aConfig['email']['ssl'],
                        'port'=>$aConfig['email']['port'],
        );
        $server        = $aConfig['email']['server'];

        $transport = new Zend_Mail_Transport_Smtp($server, $emailConfig);
        Zend_Mail::setDefaultTransport($transport);
    }
	
}

