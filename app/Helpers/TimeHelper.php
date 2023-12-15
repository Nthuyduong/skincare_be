<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    public static function convertTimeZone($time, $formatCurrent = "Y-m-d H:i:s", $format = "Y-m-d H:i:s", $timezoneCurrent = "UTC", $timezone = "Asia/Tokyo")
    {
        try {
            $date = Carbon::createFromFormat($formatCurrent, $time, $timezoneCurrent);
            $d = $date->tz($timezone);
            return $d->format($format);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function formatTime($time, $formatCurrent = "Y-m-d H:i:s", $format = "Y-m-d H:i:s")
    {
        try {
            $date = Carbon::createFromFormat($formatCurrent, $time);
            return $date->format($format);
        } catch (\Exception $e) {
            return "";
        }
    }
}
