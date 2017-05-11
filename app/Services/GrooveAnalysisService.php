<?php

namespace App\Services;

class GrooveAnalysisService
{
    private $raw;

    private static function avg($valueSet)
    {
        return array_sum($valueSet) / count($valueSet);
    }

    private static function avgUnder($valueSet, $threshold)
    {
        $filtered = array_filter($valueSet, function ($value) use ($threshold) { return $value < $threshold; });

        if (count($filtered)) {
            return self::avg($filtered);
        }

        return $threshold;
    }

    private static function avgOver($valueSet, $threshold)
    {
        $filtered = array_filter($valueSet, function ($value) use ($threshold) { return $value > $threshold; });

        if (count($filtered)) {
            return self::avg($filtered);
        }

        return $threshold;
    }

    public function __construct($rawReviewFrames)
    {
        $this->raw = $rawReviewFrames;
    }

    public function calculate()
    {
        $calculated = [];

        foreach ($this->getSortedByPosition() as $second => $values) {
            $mean = self::avg($values);
            $calculated[] = [
                'position' => $second,
                'avg_groove' => $mean,
                'high_avg_groove' => self::avgOver($values, $mean),
                'low_avg_groove'=> self::avgUnder($values, $mean),
            ];
        }

        return $calculated;
    }

    private function getSortedByPosition()
    {
        $sorted = [];

        for ($i = 0, $len = count($this->raw); $i < $len; $i++) {
            $value = $this->raw[$i]['groove'];
            $position =  (int)floor($this->raw[$i]['position']);

            if (array_key_exists($position, $sorted)) {
                $sorted[$position][] = $value;
            } else {
                $sorted[$position] = [$value];
            }
        }

        return $sorted;
    }
}
