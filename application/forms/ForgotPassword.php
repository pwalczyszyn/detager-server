<?php

class Default_Form_ForgotPassword extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email address:',
            'required'   => true,
            'filters'    => array('StringTrim')
        ));
		
        // Add the submit button
        $this->addElement('submit', 'send', array(
            'ignore'   => true,
            'label'    => 'Send',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }

}

