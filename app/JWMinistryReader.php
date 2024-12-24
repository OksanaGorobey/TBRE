<?php

class JWMinistryReader
{
    private string $currentDate;

    const MINISTRY_TABLES = [
        [
            'id' => 'ObolonMinska',
            'name' => 'Минская',
            'token' => '158hhMSf3rXB2mhACjjO95IjmSS14NEOwgQf2sfM1z_0'
        ],
        [
            'id' => 'ObolonMinMcd',
            'name' => 'Минская Макдональдс',
            'token' => '16B7FcH-J-3mgxE55WvsZRV4QAVi6y-btwTXQuREsn90'
        ],
        [
            'id' => 'ObolonCentNab',
            'name' => 'Набережная',
            'token' => '1VQt0FDAhLl2Hn6dvN5MvTl-JhrstsdEZS-lla90TlF8'
        ]
    ];

    const DATE_LANG = [
        //month
        'January' => 'января',
        'February' => 'февраля',
        'March' => 'марта',
        'April' => 'апреля',
        'May' => 'мая',
        'June' => 'июня',
        'July ' => 'июля',
        'August' => 'августа',
        'September' => 'сентября',
        'October' => 'октября',
        'November' => 'ноября',
        'December' => 'декабря',
        ///weekdays
        'Sunday' => 'Воскресенье',
        'Monday' => 'Понедельник',
        'Tuesday' => 'Вторник',
        'Wednesday' => 'Среда',
        'Thursday' => 'Четверг',
        'Friday' => 'Пятница',
        'Saturday' => 'Cуббота'

    ];

    public function execute()
    {
        $this->readCSV();
    }

    private function readCSV(): void {
        foreach (self::MINISTRY_TABLES as $table) {
            $prevStateFile = "/var/www/project/var/previousStates/{$table['id']}.json";

            $content = file_get_contents("https://docs.google.com/spreadsheets/d/{$table['token']}/export?format=csv");
            $prevState = file_get_contents($prevStateFile);

            $prevSheetInfo = json_decode($prevState, true);

            $i = 0;
            $tableInfo = [];
            foreach (explode("\n", $content) as $line) {
                $i++;

                if($i === 1) {
                    continue;
                }

                $row = array_map('trim', explode(',', trim($line)));

                if(!empty($row[0])) {
                    $this->currentDate = $row[0];
                }

                $partners = array_filter($row, fn($v, $k) => !in_array($k, [0,1]) && !empty($v), ARRAY_FILTER_USE_BOTH);

                if(!empty($partners) &&
                    !(
                        strtotime(date('Y-m') . '-' . $this->getDay()) < strtotime(date('Y-m-d'))
                    )
                ) {

                    if (count($partners) === 1 &&
                        (
                            !isset($prevSheetInfo[$this->currentDate]) ||
                            !isset($prevSheetInfo[$this->currentDate][$row[1]]) ||
                            !(!array_diff($prevSheetInfo[$this->currentDate][$row[1]], $partners))
                        )
                    ) {
                        $message = "{$this->generateTitleDate()} смена {$row[1]} на {$table['name']}\n" .
                            "<b>Записался 1 возвещатель.</b>\n" .
                            "Нужно еще 3 возвещателя! \n\n" .
                            "<a href='tg://resolve?domain=jw_telega_bot&text=/s_{$table['id']}_{$this->generateLinkDate($row[1])}'>Записаться</a>";

                        (new TelegramBot())->sendMessage($message);

                    }

                    $tableInfo[$this->currentDate][$row[1]] = $partners;
                }
            }

            file_put_contents($prevStateFile, json_encode($tableInfo, JSON_UNESCAPED_UNICODE));
        }
    }

    private function getDay(): string
    {
        $day = explode(' ', $this->currentDate)[0];
        return (int)$day < 10 ? "0{$day}" : $day;
    }

    private function generateTitleDate(): string
    {
        return str_ireplace(
            array_keys(self::DATE_LANG),
            array_values(self::DATE_LANG),
            date('l, j F', strtotime(date('Y-m') . '-' . $this->getDay()))
        );

    }

    private function generateLinkDate(string $time): string
    {
        $time = explode(' - ', $time)[0];
        $time = explode(':', $time)[0];
        $time = (int)$time < 10 ? "0{$time}" : $time;

        return $this->getDay() . date('my') . "_{$time}00";

    }
}