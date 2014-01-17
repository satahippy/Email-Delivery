<?php

namespace Sata\EmailDelivery;

class SmtpMailer
{
	public $sender;

	protected $swift;

	public function __construct($host, $user, $pass, $port, $encryption = null)
	{
		$this->swift = \Swift_Mailer::newInstance(\Swift_SmtpTransport::newInstance($host, $port, $encryption)
				->setUsername($user)
				->setPassword($pass)
		);
		//$this->swift = \Swift_Mailer::newInstance(\Swift_MailTransport::newInstance());
	}

	public function send($message)
	{
		return $this->swift->send($message);
	}

}