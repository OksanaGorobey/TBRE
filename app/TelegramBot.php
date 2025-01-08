<?php

class TelegramBot
{
    private string $token = '7368479298:AAHP6SehCA3eT51nNAQ_Ho5YULf8VJHACg4';
    private string $chat_id = '@checkChangeExcel';

    public function sendMessage($message, string $chatId = ''): void
    {
        /** $curl CurlHandle */
        $curl = curl_init("https://api.telegram.org/bot{$this->token}/sendMessage");

        //set settings
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            [
                'chat_id' => $chatId ?: $this->chat_id,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        
        $res = curl_exec($curl);

        curl_close($curl);

        //log
        (new Log())->write($res);
    }
}