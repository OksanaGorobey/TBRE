<?php

class Log
{
    public string $filename = '/var/www/project/var/log.txt';

    public function write($message): void {
        $somecontent = date('Y-m-d H:i:s') . ' ' . json_encode(json_decode($message, JSON_HEX_TAG), JSON_UNESCAPED_UNICODE) . "\n";

        if (is_writable($this->filename)) {
            if (!$fp = fopen($this->filename, 'a')) {
                exit;
            }

            if (fwrite($fp, $somecontent) === FALSE) {
                exit;
            }

            fclose($fp);
        }
    }
}