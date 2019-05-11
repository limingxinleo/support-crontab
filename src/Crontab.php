<?php

declare(strict_types=1);
/**
 * This file is part of 李铭昕.
 *
 * @contact  limingxin@swoft.org
 */

namespace limx\Support;

class Crontab
{
    /**
     * Find out if a given timestamp is currently executing timestamp by analyzing the crontab syntax.
     *
     * @return bool
     */
    public static function current(string $cron, ?int $time = null)
    {
        $date = self::parse($cron);
        $start = $time ?? time();

        if (in_array(intval(date('j', $start)), $date['dom']) &&
            in_array(intval(date('n', $start)), $date['month']) &&
            in_array(intval(date('w', $start)), $date['dow']) &&
            in_array(intval(date('G', $start)), $date['hours']) &&
            in_array(intval(date('i', $start)), $date['minutes'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Finds next execution timestamp after given starting timestamp by analyzing crontab syntax.
     *
     * @return null|int
     */
    public static function next(string $cron, ?int $time = null)
    {
        $date = self::parse($cron);
        $start = $time ?? time();

        for ($i = 0; $i <= 60 * 60 * 24 * 366; $i += 60) {
            if (in_array(intval(date('j', $start + $i)), $date['dom']) &&
                in_array(intval(date('n', $start + $i)), $date['month']) &&
                in_array(intval(date('w', $start + $i)), $date['dow']) &&
                in_array(intval(date('G', $start + $i)), $date['hours']) &&
                in_array(intval(date('i', $start + $i)), $date['minutes'])
            ) {
                return $start + $i;
            }
        }
        return null;
    }

    /**
     * @param string $_cron_string :
     *
     *      0     1    2    3    4
     *      *     *    *    *    *
     *      -     -    -    -    -
     *      |     |    |    |    |
     *      |     |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *      |     |    |    +------- month (1 - 12)
     *      |     |    +--------- day of month (1 - 31)
     *      |     +----------- hour (0 - 23)
     *      +------------- min (0 - 59)
     * @throws InvalidArgumentException
     */
    public static function parse(string $cronString)
    {
        if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($cronString))) {
            throw new \InvalidArgumentException('Invalid cron string: ' . $cronString);
        }

        $cron = preg_split('/[\\s]+/i', trim($cronString));

        return ['minutes' => self::parseCronNumbers($cron[0], 0, 59),
            'hours' => self::parseCronNumbers($cron[1], 0, 23),
            'dom' => self::parseCronNumbers($cron[2], 1, 31),
            'month' => self::parseCronNumbers($cron[3], 1, 12),
            'dow' => self::parseCronNumbers($cron[4], 0, 6),
        ];
    }

    /**
     * get a single cron style notation and parse it into numeric value.
     *
     * @param string $s cron string element
     * @param int $min minimum possible value
     * @param int $max maximum possible value
     * @return int parsed number
     */
    protected static function parseCronNumbers($s, $min, $max)
    {
        $result = [];
        $v = explode(',', $s);
        foreach ($v as $vv) {
            $vvv = explode('/', $vv);
            $step = empty($vvv[1]) ? 1 : $vvv[1];
            $vvvv = explode('-', $vvv[0]);
            $_min = count($vvvv) == 2 ? $vvvv[0] : ($vvv[0] == '*' ? $min : $vvv[0]);
            $_max = count($vvvv) == 2 ? $vvvv[1] : ($vvv[0] == '*' ? $max : $vvv[0]);
            for ($i = $_min; $i <= $_max; $i += $step) {
                $result[$i] = intval($i);
            }
        }
        ksort($result);
        return $result;
    }
}
