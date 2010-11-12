<?php

class Default_Form_Register extends Zend_Form
{

	public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        $this->addElement('text', 'username', array(
        	'label'      => 'Username:',
    		'validators' => array(
        		array('StringLength', false, array(3, 20))
        		,
        		array('alnum')
//        		, 
//        		array('regex', false, array('/^[a-zA-Z0-9_]$/'))
        		, 
        		new Zend_Validate_Db_NoRecordExists("dt_users","username")
			),
    		'required' => true,
    		'filters'  => array('StringToLower', 'StringTrim')
		));
        
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email address:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            	'EmailAddress'
        		, new Zend_Validate_Db_NoRecordExists("dt_users","email")
        	)
        ));
		
        // Add a password
        $this->addElement('password', 'password', array(
            'label'      => 'Password:',
            'required'   => true,
            'validators' => array( 
        		array('NotEmpty', true),  
        		array('StringLength', false, array(6, 32))
        	)
        ));
        
        // Add the submit button
        $this->addElement('submit', 'register', array(
            'ignore'   => true,
            'label'    => 'Register',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}
