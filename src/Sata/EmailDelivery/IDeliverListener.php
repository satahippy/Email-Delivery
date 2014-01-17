<?php

namespace Sata\EmailDelivery;


interface IDeliverListener
{
	public function onBegin($deliver);
	public function onEnd($deliver);
	public function onBeforeSend($mailer, $message);
	public function onAfterSend($mailer, $message);
} 