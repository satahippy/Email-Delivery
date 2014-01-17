<?php

namespace Sata\EmailDelivery;

class MailersDbProvider
{
	protected $db;

	private $offset = 0;
	private $_count;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function addSender($sender)
	{
		return $this->db->prepare('INSERT INTO senders (address, name, host, user, pass, port, encryption) VALUES(:address, :name, :host, :user, :pass, :port, :encryption)')
			->execute(array(
				':address' => $sender['address'],
				':name' => isset($sender['name']) ? $sender['name'] : null,
				':host' => $sender['host'],
				':user' => $sender['user'],
				':pass' => $sender['pass'],
				':port' => $sender['port'],
				':encryption' => isset($sender['encryption']) ? $sender['encryption'] : null
			));
	}

	public function next()
	{
		$count = $this->count();
		if ($count === 0) {
			throw new \Exception('Senders list is empty.');
		}
		if ($this->offset >= $count) {
			$this->offset = 0;
		}

		$row = $this->db->query("SELECT * FROM senders LIMIT {$this->offset}, 1")->fetch();
		$mailer = new SmtpMailer($row['host'], $row['user'], $row['pass'], (int)$row['port'], empty($row['encryption']) ? null : $row['encryption']);
		$mailer->sender = array($row['address'] => empty($row['name']) ? null : $row['name']);

		$this->offset++;
		return $mailer;
	}

	public function count($refresh = false)
	{
		if ($this->_count === null || $refresh) {
			$this->_count = (int)$this->db->query("SELECT COUNT(*) FROM senders")->fetchColumn();
		}
		return $this->_count;
	}

} 