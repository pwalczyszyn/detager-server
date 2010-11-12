<?php

class Default_Helper_Mail extends Zend_View
{

	private $template;
	
	private $recipient;
	
	private $recipientName;
	
	private $subject;
	
	public $url;
	
   	function __construct($recipient, $recipientName, $subject, $template) 
   	{
		parent::__construct();
		$this->template = $template;
		$this->recipient = $recipient;
		$this->recipientName = $recipientName;
		$this->subject = $subject;
		
		$config = Zend_Registry::get('config');
		$this->url = $config['url'];
		
		$this->addScriptPath(APPLICATION_PATH . '/views/mails/');
   }

   public function send()
   {
		$htmlBody = $this->render($this->template);
	
	    $mail = new Zend_Mail();
		$mail->setBodyHtml($htmlBody);
		$mail->setFrom('no-reply@detager.com', 'Detager');
		$mail->addTo($this->recipient, $this->recipientName);
		$mail->setSubject($this->subject);
		$mail->send();
   }
   
}