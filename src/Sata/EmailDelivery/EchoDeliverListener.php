<?php

namespace Sata\EmailDelivery;


class EchoDeliverListener implements IDeliverListener
{
	public function onBegin($deliver)
	{
		echo 'Start Delivering' . PHP_EOL;
	}

	public function onEnd($deliver)
	{
		echo 'End Delivering' . PHP_EOL;
	}

	public function onBeforeSend($mailer, $message)
	{
	}

	public function onAfterSend($mailer, $message)
	{
		$sender = is_array($mailer->sender) ? array_keys($mailer->sender)[0] : $mailer->sender;
		echo 'Sent To ' . implode(', ', array_keys($message->getTo())) . ' FROM ' . $sender . PHP_EOL;
	}
} 