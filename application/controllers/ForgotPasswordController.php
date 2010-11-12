<?php

class ForgotPasswordController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$form = new Default_Form_ForgotPassword();
		$this->view->form = $form;

		if ($this->getRequest()->isPost())
		{
			$formData = $this->_request->getPost();

			if ($form->isValid($formData))
			{

				$emailConfirmationId = md5(uniqid());
				$db = Zend_Db_Table::getDefaultAdapter();
				 
				$userTable = new Default_Dao_User();

				$updatedRowsCount = $userTable->update(
					array('email_confirmation_id' => $emailConfirmationId),
					array($db->quoteInto('email = ?', $formData['email']), 'valid is true'));

				if ($updatedRowsCount == 1)
				{
					$mail = new Default_Helper_Mail(
					$formData['email'],
        	                    	'', 
        	                    	'Detager: change password request', 
        	                    	'changePasswordInstructions.phtml');
					$mail->emailConfirmationId = $emailConfirmationId;
					$mail->send();
				}
				 
				$this->render('confirm');
			}
		}
	}

	public function confirmAction()
	{
		// action body
	}

	public function changeAction()
	{
		$form = new Default_Form_ChangePassword();
    	if ($this->getRequest()->isGet())
    	{
    		$ecid = $this->_request->getParam('ecid', null);
    		if ($ecid != null)
    		{
				$form->ecid = $ecid;
				$ecidPost = $form->createElement('hidden', 'ecid_post');
        		$ecidPost->setValue($ecid)
					->removeDecorator('label')
					->removeDecorator('HtmlTag');
					
        		$form->addElement($ecidPost);
				
				$this->view->form = $form;
    		}
    	}
    	else if ($this->getRequest()->isPost())
		{
			$formData = $this->_request->getPost();
			if ($form->isValid($formData))
			{
				$this->view->formData = $formData;
				
				$db = Zend_Db_Table::getDefaultAdapter();
				$userTable = new Default_Dao_User();
				$updatedRowsCount = $userTable->update(
					array('password' => md5($formData['password']), 'email_confirmation_id' => null),
					array($db->quoteInto('email_confirmation_id = ?', $formData['ecid_post']), 'valid is true'));
			}
		}
	}
}
