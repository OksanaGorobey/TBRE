<?php

require_once(__DIR__.'/JWMinistryReader.php');

//(new JWMinistryReader())->execute();



$curl = curl_init("https://api.telegram.org/bot7368479298:AAHP6SehCA3eT51nNAQ_Ho5YULf8VJHACg4/sendMessage");

//set settings
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt(
    $curl,
    CURLOPT_POSTFIELDS,
    [
        'chat_id' => '@jw_telega_bot',
        'text' => '$message'
    ]
);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);

$res = curl_exec($curl);

curl_close($curl);

var_dump($res);
?>