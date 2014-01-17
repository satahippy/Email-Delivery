<?php

namespace Sata\EmailDelivery;


class Deliver
{
	public $mailersProvider;
	public $messagesProvider;

	public $pause = 0;

	public $listeners = array();

	public function start()
	{
		$this->event('begin', array($this));
		while (($message = $this->messagesProvider->next()) !== false) {
			$mailer = $this->mailersProvider->next();
			$message->setFrom($mailer->sender);
			$this->event('beforeSend', array($mailer, $message));
			$mailer->send($message);
			$this->event('afterSend', array($mailer, $message));
			sleep($this->pause);
		}
		$this->event('end', array($this));
	}

	protected function event($event, $arguments = array())
	{
		foreach ($this->listeners as $listener) {
			call_user_func_array(array($listener, 'on' . $event), $arguments);
		}
	}

	public function addListener($listener)
	{
		$this->listeners[] = $listener;
	}

} 