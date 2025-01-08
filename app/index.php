<?php
echo "Hello World!";
require_once(__DIR__.'/JWMinistryReader.php');
require_once(__DIR__ . '/TelegramBot.php');
require_once(__DIR__ . '/Log.php');

(new JWMinistryReader())->execute();

?>