<?php

namespace ODB\DB\Operations;

trait Functions
{

public function now($diff = null, $func = "NOW()")
    {
        return ["[F]" => [$this->interval($diff, $func)]];
    }
public function interval($diff, $func = "NOW()")
    {
        $types = ["s" => "second", "m" => "minute", "h" => "hour", "d" => "day", "M" => "month", "Y" => "year"];
        $incr  = '+';
        $items = '';
        $type  = 'd';

        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }

            if (!empty($matches[2])) {
                $items = $matches[2];
            }

            if (!empty($matches[3])) {
                $type = $matches[3];
            }

            if (!in_array($type, array_keys($types))) {
                throw new Exception("invalid interval type in '{$diff}'");
            }

            $func .= " ".$incr." interval ".$items." ".$types[$type]." ";
        }
        return $func;
    }

    /**
     * Method generates incremental function call
     *
     * @param int $num increment by int or float. 1 by default
     * @throws Exception
     * @return array
     */
    public function inc($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('Argument supplied to inc must be a number');
        }
        return ["[I]" => "+".$num];
    }

    /**
     * Method generates user defined function call
     *
     * @param string $expr user function body
     * @param array $bindParams
     * @return array
     */
    public function func($expr, $bindParams = null)
    {
        return ["[F]" => [$expr, $bindParams]];
    }

     /**
     * Method generates decrimental function call
     *
     * @param int $num increment by int or float. 1 by default
     * @return array
     */
    public function dec($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('Argument supplied to dec must be a number');
        }
        return array("[I]" => "-".$num);
    }

}