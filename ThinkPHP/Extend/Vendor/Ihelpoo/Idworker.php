<?php
class Idworker
{
    const debug = 0;
    static $workerId;
    static $twepoch = 1361775855078;
    static $sequence = 0;
    const workerIdBits = 4;
    static $maxWorkerId = 15;
    const sequenceBits = 10;
    static $workerIdShift = 10;
    static $timestampLeftShift = 14;
    static $sequenceMask = 1023;
    private static $lastTimestamp = -1;

    function __construct($workId)
    {
        if ($workId > self::$maxWorkerId || $workId < 0) {
            throw new Exception("worker Id can't be greater than 15 or less than 0");
        }
        self::$workerId = $workId;

        echo 'logdebug->__construct()->self::$workerId:' . self::$workerId;
        echo '</br>';

    }

    function timeGen()
    {
//获得当前时间戳
        $time = explode(' ', microtime());
        $time2 = substr($time[0], 2, 3);
        $timestramp = $time[1] . $time2;
        echo 'logdebug->timeGen()->$timestramp:' . $time[1] . $time2;
        echo '</br>';
        return $time[1] . $time2;
    }

    function  tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        echo 'logdebug->tilNextMillis()->$timestamp:' . $timestamp;
        echo '</br>';
        return $timestamp;
    }

    function  nextId()
    {
        $timestamp = $this->timeGen();
        echo 'logdebug->nextId()->self::$lastTimestamp1:' . self::$lastTimestamp;
        echo '</br>';
        if (self::$lastTimestamp == $timestamp) {
            self::$sequence = (self::$sequence + 1) & self::$sequenceMask;
            if (self::$sequence == 0) {
                echo "###########" . self::$sequenceMask;
                $timestamp = $this->tilNextMillis(self::$lastTimestamp);
                echo 'logdebug->nextId()->self::$lastTimestamp2:' . self::$lastTimestamp;
                echo '</br>';
            }
        } else {
            self::$sequence = 0;
            echo 'logdebug->nextId()->self::$sequence:' . self::$sequence;
            echo '</br>';
        }
        if ($timestamp < self::$lastTimestamp) {
            throw new Excwption("Clock moved backwards.  Refusing to generate id for " . (self::$lastTimestamp - $timestamp) . " milliseconds");
        }
        self::$lastTimestamp = $timestamp;
        echo 'logdebug->nextId()->self::$lastTimestamp3:' . self::$lastTimestamp;
        echo '</br>';

        echo 'logdebug->nextId()->(($timestamp - self::$twepoch << self::$timestampLeftShift )):' . ((sprintf('%.0f', $timestamp) - sprintf('%.0f', self::$twepoch)));
        echo '</br>';
        $nextId = ((sprintf('%.0f', $timestamp) - sprintf('%.0f', self::$twepoch))) | (self::$workerId << self::$workerIdShift) | self::$sequence;
        echo 'timestamp:' . $timestamp . '-----';
        echo 'twepoch:' . sprintf('%.0f', self::$twepoch) . '-----';
        echo 'timestampLeftShift =' . self::$timestampLeftShift . '-----';
        echo 'nextId:' . $nextId . '----';
        echo 'workId:' . self::$workerId . '-----';
        echo 'workerIdShift:' . self::$workerIdShift . '-----';
        return $nextId;
    }

}


?>

