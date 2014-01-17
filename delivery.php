<?php

require_once 'vendor/autoload.php';

use Commando\Command;
use Sata\EmailDelivery\MailersDbProvider;
use Sata\EmailDelivery\MessagesDbProvider;
use Sata\EmailDelivery\Deliver;
use Sata\EmailDelivery\EchoDeliverListener;

class DeliveryCli
{
	protected $cli;
	protected $db;

	protected $options = array();
	public $defaults = array(
		'db' => 'delivery.sqlite',
		'e' => false,
		'ef' => false,
		'm' => false,
		'mf' => false,
		'start' => false,
		'cq' => false,
		'as' => false,
		'count' => 5,
		'pause' => 3
	);

	private $deliver;
	private $mailersProvider;
	private $messagesProvider;

	public function __construct(Command $cli = null, $setOptions = true)
	{
		if ($cli === null) {
			$this->cli = new Command();
		} else {
			$this->cli = $cli;
		}

		if ($setOptions) {
			$this->setOptions();
		}
	}

	protected function setOptions()
	{
		$this->cli->option('db')->aka('database')->describedAs('Database File');
		$this->cli->option('e')->aka('emails')->describedAs('Comma Separated Receives Emails List');
		$this->cli->option('ef')->aka('emails-file')->describedAs('File With Receives Emails');
		$this->cli->option('m')->aka('message')->describedAs('Delivery Message');
		$this->cli->option('mf')->aka('messages-file')->describedAs('Delivery Messages File');
		$this->cli->option('start')->bool()->describedAs('Start Delivery');
		$this->cli->option('cq')->aka('clear-queue')->bool()->describedAs('Clear Current Messages Queue');
		$this->cli->option('count')->describedAs('Count Receivers In Delivery');
		$this->cli->option('pause')->describedAs('Pause Time (in seconds)');
		$this->cli->option('as')->aka('add-senders')->describedAs('Add To Senders List From File');
		$this->cli->setHelp('Tool For Email Delivery');

		foreach ($this->defaults as $key => $val) {
			if ($this->cli->hasOption($key) && $this->cli[$key] !== null) {
				$this->options[$key] = $this->cli[$key];
			} else {
				$this->options[$key] = $val;
			}
		}
	}

	protected function checkOptions()
	{
		if (empty($this->options['db'])) {
			return false;
		}
		if ((!empty($this->options['e']) || !empty($this->options['ef'])) && (empty($this->options['m']) && empty($this->options['mf']))) {
			return false;
		}
		if ((!empty($this->options['m']) || !empty($this->options['mf'])) && (empty($this->options['e']) && empty($this->options['ef']))) {
			return false;
		}
		return true;
	}

	public function run()
	{
		if ($this->checkOptions()) {
			try {
				echo 'Connecting To Database...' . PHP_EOL;
				$this->connectDB($this->options['db']);

				if (!empty($this->options['as'])) {
					echo 'Adding Senders...' . PHP_EOL;
					$this->addSenders($this->options['as']);
				}

				if ($this->options['cq']) {
					echo 'Clearing Queue...' . PHP_EOL;
					$this->getMessageProvider()->clear();
				}

				$this->addMessages($this->options['m'], $this->options['mf'], $this->options['e'], $this->options['ef'], (int)$this->options['count']);

				if ($this->options['start']) {
					$this->getDeliver()->start();
				}
			} catch (Exception $e) {
				$this->cli->error($e);
			}
		} else {
			$this->cli->printHelp();
		}
	}

	public function connectDB($path)
	{
		$this->db = new PDO("sqlite:" . $path);
	}

	public function addSenders($file)
	{
		$mailersProvider = $this->getMailersProvider();
		$senders = require $file;
		foreach ($senders as $sender) {
			if ($mailersProvider->addSender($sender)) {
				echo 'Added address ' . $sender['address'] . PHP_EOL;
			} else {
				echo 'Address ' . $sender['address'] . ' already exists' . PHP_EOL;
			}
		}
	}

	public function addMessages($message, $messagesFile, $email, $emailsFile, $countEmailsInMessage)
	{
		$messages = array();
		$emails = array();

		if (!empty($message)) {
			$messages = array(array('content' => $message, 'html' => false, 'subject' => ''));
		} elseif (!empty($messagesFile)) {
			$messages = require $messagesFile;
		}

		if (!empty($email)) {
			$emails = explode(',', $email);
		} elseif (!empty($emailsFile)) {
			$emails = fopen($emailsFile, "r");
		}

		if (count($messages) && (is_resource($emails) || count($emails))) {
			echo 'Adding messages to queue...' . PHP_EOL;
			$receivers = array();
			while (($email = $this->getNextEmail($emails)) !== false) {
				$receivers[] = $email;
				if (count($receivers) == $countEmailsInMessage) {
					$this->addMessagesToProvider($receivers, $messages);
					$receivers = array();
				}
			}
			$this->addMessagesToProvider($receivers, $messages);
		}
		if (is_resource($emails)) {
			fclose($emails);
		}
	}

	protected function getNextEmail(&$emails)
	{
		if (is_resource($emails)) {
			$email = fscanf($emails, "%s\n");
			if ($email === false) {
				return false;
			}
			return $email[0];
		} else {
			if (!count($emails)) {
				return false;
			} else {
				return array_shift($emails);
			}
		}
	}

	protected function addMessagesToProvider($emails, $messages)
	{
		foreach ($messages as $message) {
			$this->getMessageProvider()->add($emails, $message);
		}
	}

	public function getDeliver()
	{
		if ($this->deliver === null) {
			$this->deliver = new Deliver();
			$this->deliver->messagesProvider = $this->getMessageProvider();
			$this->deliver->mailersProvider = $this->getMailersProvider();
			$this->deliver->pause = $this->options['pause'];
			$this->deliver->addListener(new EchoDeliverListener());
		}
		return $this->deliver;
	}

	public function getMailersProvider()
	{
		if ($this->mailersProvider === null) {
			$this->mailersProvider = new MailersDbProvider($this->db);
		}
		return $this->mailersProvider;
	}

	public function getMessageProvider()
	{
		if ($this->messagesProvider === null) {
			$this->messagesProvider = new MessagesDbProvider($this->db);
		}
		return $this->messagesProvider;
	}

}

Swift_DependencyContainer::getInstance()
	->register('mime.headerfactory')
	->asNewInstanceOf('Swift_Mime_SimpleHeaderFactory')
	->withDependencies(array(
		'mime.base64headerencoder',
		'mime.rfc2231encoder',
		'mime.grammar',
		'properties.charset'
	));

$delivery = new DeliveryCli();
$delivery->run();