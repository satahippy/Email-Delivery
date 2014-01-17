<?php
return array(
	array(
		'content' => 'Это тестовое сообщение не html',
		'html' => false,
		'subject' => 'Это тема письма'
	),
	array(
		'content' => 'Это тестовое <b>html</b> сообщение',
		'html' => true,
		'subject' => 'Это тема HTML письма'
	)
);
?>