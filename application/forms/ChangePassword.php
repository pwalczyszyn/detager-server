<?php

class Default_Form_ChangePassword extends Zend_Form
{

	public $ecid;
	
    public function init()
    {
    	$this->setMethod('post');
    	
        // Add a password
        $this->addElement('password', 'password', array(
            'label'      => 'New password:',
            'required'   => true,
            'validators' => array( 
        		array('NotEmpty', true),  
        		array('StringLength', false, array(6, 32))
        	)
        ));
        
//        $this->addElement('hidden', 'ecid_post', 
//        	array('value' => $this->ecid, 'disableLoadDefaultDecorators' => true));
        
//        $ecidPost = $this->createElement('hidden', 'ecid_post');
//        $ecidPost->setValue($this->ecid)
//        	->removeDecorator('label')
//             ->removeDecorator('HtmlTag');
//        $this->addElement($ecidPost);
        	
        // Add the submit button
        $this->addElement('submit', 'change', array(
            'ignore'   => true,
            'label'    => 'Change',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }


}

