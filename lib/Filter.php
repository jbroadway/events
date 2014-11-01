<?php

namespace events;

class Filter
{
    public static function mon($v)
    {
        $orig = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec';
        $keys = explode (' ', $orig);
        $vals = explode (' ', __ ($orig));
        $list = array_combine ($keys, $vals);

        return $list[$v];
    }

    public static function month($v)
    {
        $orig = 'January February March April May June July August September October November December';
        $keys = explode (' ', $orig);
        $vals = explode (' ', __ ($orig));
        $list = array_combine ($keys, $vals);

        return $list[$v];
    }

    public static function day($v)
    {
        $orig = 'Sun Mon Tue Wed Thu Fri Sat';
        $keys = explode (' ', $orig);
        $vals = explode (' ', __ ($orig));
        $list = array_combine ($keys, $vals);

        return $list[$v];
    }

    public static function fullday($v)
    {
        $orig = 'Sunday Monday Tuesday Wednesday Thursday Friday Saturday';
        $keys = explode (' ', $orig);
        $vals = explode (' ', __ ($orig));
        $list = array_combine ($keys, $vals);

        return $list[$v];
    }

    public static function shortdate($d)
    {
        $d = strtotime ($d);

        return self::mon (gmdate ('M', $d)) . ' ' . gmdate ('j', $d);
    }

    public static function shortdaydate($d)
    {
        $d = strtotime ($d);

        return self::day (gmdate ('D', $d)) . ', ' . self::mon (gmdate ('M', $d)) . ' ' . gmdate ('j', $d);
    }

    public static function date($d)
    {
        $d = strtotime ($d);

        return self::month (gmdate ('F', $d)) . ' ' . gmdate ('j, Y', $d);
    }

    public static function time($t)
    {
        $t = strtotime ($t);

        return gmdate ('g:ia', $t);
    }
}
