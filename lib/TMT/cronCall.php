<?php

$domainUrl = "http://the-music-tank.herokuapp.com/";


# Execute daily cron

// Get cURL resource
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $domainUrl . 'cron/daily',
    CURLOPT_USERAGENT => 'TMT daily cURL Request'
));
$resp = curl_exec($curl);
curl_close($curl);


# Execute weekly cron, if we are at the begining of a week

$weekMondayTime = strtotime('Monday this week');
$weekTuesdayTime = strtotime('Tuesday this week');
$now = time();
if($now > $weekMondayTime && $now < $weekTuesdayTime)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $domainUrl . '/cron/weekly',
        CURLOPT_USERAGENT => 'TMT weekly cURL Request'
    ));
    $resp = curl_exec($curl);
    curl_close($curl);
}