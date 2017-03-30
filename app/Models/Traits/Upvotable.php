<?php

namespace App\Models\Traits;

trait Upvotable
{
    public static function shouldAddVote($value)
    {
        return (int)$value > 0;
    }

    public static function shouldRemoveVote($value)
    {
        return (int)$value === -1;
    }
}
