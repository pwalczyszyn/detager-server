<?php

class RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $form = new Default_Form_Register();
        $this->view->form = $form;
                        
        if ($this->getRequest()->isPost()) 
        {
			$formData = $this->_request->getPost();
                            
            if ($form->isValid($formData)) 
            {     			
                $this->insertUser($formData);
                $this->render('confirm');
            }
        }
    }

    private function insertUser($formData)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
       	$db->beginTransaction();
                	
		try
		{    	
			$emailConfirmationId = md5(uniqid());
			$entity =
                    	array(
                    		'username' => $formData['username'],
                    		'password' => md5($formData['password']),
                    		'email' => $formData['email'],
                    		'email_confirmation_id' => $emailConfirmationId,
                    		'entry_date' => new Zend_Db_Expr('NOW()') 
                    	);
                    
                    $user = new Default_Dao_User();
                    $userId = $user->insert($entity);
                    		    
                    $mail = new Default_Helper_Mail(
                    	$formData['email'], 
                    	'', 
                    	'Detager: activate your account', 
                    	'registrationConfirmation.phtml');
                    $mail->emailConfirmationId = $emailConfirmationId;
                	$mail->send();
                	
                	$db->commit();
                }
                catch (Exception $e)
                {
                	$db->rollBack();
                	throw $e;
                }
    }

    public function confirmAction()
    {
        // action body
    }

    public function activateAction()
    {
    	if ($this->getRequest()->isGet())
    	{
    		$ecid = $this->_request->getParam('ecid', null);
    		if ($ecid != null)
    		{
    			$db = Zend_Db_Table::getDefaultAdapter();
				$userTable = new Default_Dao_User();
				$stmt = $userTable->select()
					->where($db->quoteInto("email_confirmation_id = ?", $ecid))
					->where("valid is false");
				$row = $userTable->fetchRow($stmt);
				
				if ($row != null)
				{
		    		$row['valid'] = true;
		    		$row['email_confirmation_id'] = null;
		    		$row->save();
		    	}
    		}
    	}
    }

}





