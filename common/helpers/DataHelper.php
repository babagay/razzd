<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\helpers;

use Yii;

/**
 * ArrayHelper provides additional array functionality that you can use in your
 * application.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DataHelper {

    public function treeMap($dataset, $childKey = 'children') {

        $tree = array();
        foreach ($dataset as $id => &$node) {

            if (isset($node['pid'])) {
                if (!$node['pid']) {
                    $tree[$id] = &$node;
                } else {
                    $dataset[$node['pid']][$childKey][$id] = &$node;
                }
            } else {
                $n = array_shift($node);
                $id = key($n);
                $tree += $n;
                $tree[$id]['pid'] = 0;
            }
        }

        return $tree;
    }

    public function treeDepth($data, $max_depth, $cur_depth = 0) {
        $t = [];
        foreach ($data as $key => $itm) {
            if (isset($itm['children']) && $cur_depth + 1 > $max_depth)
                unset($itm['children']);
            elseif (isset($itm['children']))
                $itm['children'] = $this->treeDepth($itm['children'], $max_depth, $cur_depth + 1);

            $data[$key] = $itm;
        }

        return $data;
    }

    public function treeParent($data, $parent) {
        if (!$parent)
            return $data;
        $t = [];
        foreach ($data as $key => $itm) {
            if ($itm['id'] == $parent)
                return [$key => $itm];
            elseif (isset($itm['children']))
                $t = $this->treeParent($itm['children'], $parent);
        }

        return $t;
    }

    public static function numMorph($num, $str1, $str2, $str3) {
        $val = $num % 100;

        if ($val > 10 && $val < 20)
            return $num . ' ' . $str3;
        else {
            $val = $num % 10;
            if ($val == 1)
                return $num . ' ' . $str1;
            elseif ($val > 1 && $val < 5)
                return $num . ' ' . $str2;
            else
                return $num . ' ' . $str3;
        }
    }

    public function date($date) {
        //$apd = CHtml::encode(date("j.m.y H:i", $date));    // полная дата
        $mpd = date("m.y", $date);          // месяц
        $dpd = date("j", $date);            // день
        $tpd = date("H:i", $date);          // время
        $md = date("m.y");                 // месяц сегодня
        $dd = date("j");                   // день сегодня

        $today = false;
        $yesterday = false;
        // Сегодня ?
        if (($mpd == $md) & ($dpd == $dd)) {
            $today = true;
            $yesterday = false;
            return 'сегодня в ' . $tpd;
        }
        // Вчера ?
        if (($mpd == $md) & ($dpd == $dd - 1)) {
            $today = false;
            $yesterday = true;
            return 'вчера в ' . $tpd;
        }
        // Не сегодня и не вчера
        if (($today == false) & ($yesterday == false)) {
            return Yii::$app->formatter->asDate($date);
        }
    }

    /**
     * Счетчик обратного отсчета
     *
     * @param mixed $date
     * @return
     */
    public static function downcounter($date) {

        $check_time = strtotime($date) - time();
        if ($check_time <= 0) {
            return false;
        }

        $days = floor($check_time / 86400);
        $hours = floor(($check_time % 86400) / 3600);
        $minutes = floor(($check_time % 3600) / 60);
        $seconds = $check_time % 60;

        $str = '';

        if ($days > 0)
            $str .= self::numMorph($days, 'DAY', 'DAYS', 'DAYS') . ' ';
        if ($hours > 0)
            $str .= self::numMorph($hours, 'HOUR', 'HOURS', 'HOURS') . ' ';
        if ($minutes > 0)
            $str .= self::numMorph($minutes, 'MINUTE', 'MINUTES', 'MINUTES') . ' ';
        //if ($seconds > 0)
        // $str .= self::numMorph($days, 'секунда', 'секунды', 'секунд') . ' ';
        return $str;
    }

}
