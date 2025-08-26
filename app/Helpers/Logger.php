<?php
namespace App\Helpers;
class Logger
{

public static function info(string $message): void
{
$logFile = __DIR__ . '/../../storage/logs/app.log';
$time = date('Y-m-d H:i:s');
file_put_contents($logFile, "[$time] INFO: $message\n",
FILE_APPEND);
}
}