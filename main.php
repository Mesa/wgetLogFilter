#!/usr/bin/php
<?php

error_reporting(-1);

$log          = $argv[1];
$resultFormat = "%s: %-5s";

$codeFilter = array("404", 200);


if (!file_exists($log)) {
    throw new Exception("File not found[" . $log . "]");
}

$responseCodes = array();

$fh = fopen($log, "r");

$rootUrl = false;

while (!feof($fh)) {
    $line = fgets($fh);

    if (strpos($line, "Spider-Modus") === 0) {
        $url     = "";
        $year    = 0;
        $month   = 0;
        $day     = 0;
        $code    = 0;
        $message = "";

        $tmp = fgets($fh);

        preg_match('/(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2}).+(?<url>http:.+)/', $tmp, $matches);

        extract($matches, EXTR_IF_EXISTS);

        while (substr($tmp, 0, 4) !== "HTTP") {
            $tmp = fgets($fh);
        }

        preg_match('/(?<code>\d+)\s(?<message>[\w ]+)/', $tmp, $matches);
        extract($matches, EXTR_IF_EXISTS);

        if (!empty($codeFilter) && !in_array($code, $codeFilter)) {
            continue;
        }

        if (!$rootUrl) {
            $rootUrl = $url;
        }

        if (!isset($responseCodes[(int)$code])) {
            $responseCodes[(int)$code] = 0;
        }

        $responseCodes[(int)$code]++;
        $url = urldecode($url);
        $url = str_replace($rootUrl, "/", $url);
        printf("[%s] %-10s %s\n", $code, $message, $url);
    }
}

echo "\n\n";

$totalResult = 0;
foreach ($responseCodes as $code) {
    $totalResult += $code;
}

printf($resultFormat, "Seiten", $totalResult);

foreach ($responseCodes as $code => $count) {
    printf($resultFormat, $code, $count);
}


