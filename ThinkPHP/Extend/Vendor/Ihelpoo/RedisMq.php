<?php

class RedisMQ
{
    const POSITION_FIRST = 0;
    const POSITION_LAST = -1;

    public function zPop($zset)
    {
        return $this->zsetPop($zset, self::POSITION_FIRST);
    }

    public function zRevPop($zset)
    {
        return $this->zsetPop($zset, self::POSITION_LAST);
    }

    private function zsetPop($zset, $position)
    {
        $this->watch($zset);

        $element = $this->zRange($zset, $position, $position);

        if (!isset($element[0])) {
            return false;
        }

        if ($this->multi()->zRem($zset, $element[0])->exec()) {
            return $element[0];
        }

        return $this->zsetPop($zset, $position);
    }
}

?>
