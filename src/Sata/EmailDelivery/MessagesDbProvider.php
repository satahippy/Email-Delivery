<?php

namespace Sata\EmailDelivery;

class MessagesDbProvider
{
	protected $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function clear()
	{
		return $this->db->exec('DELETE FROM messages');
	}

	public function add($emails, $message)
	{
		return $this->db->prepare('INSERT INTO messages (content, html, subject, addresses) VALUES(:content, :html, :subject, :addresses)')
			->execute(array(
				':content' => $message['content'],
				':html' => (int)(isset($message['html']) ? $message['html'] : false),
				':subject' => isset($message['subject']) ? $message['subject'] : '',
				':addresses' => implode(',', $emails)
			));
	}

	public function next()
	{
		$row = $this->db->query("SELECT * FROM messages LIMIT 1")->fetch();
		if ($row === false) {
			return false;
		}
		$this->db->exec("DELETE FROM messages WHERE id = $row[id]");

		$message = \Swift_Message::newInstance($row['subject'], $row['content'], $row['html'] ? 'text/html' : 'text/plain')
			->setTo(explode(',', $row['addresses']));
		return $message;
	}

}