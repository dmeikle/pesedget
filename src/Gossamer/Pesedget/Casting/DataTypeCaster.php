<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/21/2016
 * Time: 10:31 PM
 */

namespace Gossamer\Pesedget\Casting;


class DataTypeCaster
{

    private $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function getCastValue($columnName, $value)
    {
       if(!array_key_exists($columnName, $this->columns)) {
           return $value;
       }

       return $this->castType($this->columns[$columnName]['Type'], $value);
    }

    private function castType($fieldType, $value)
    {

        $type = substr($fieldType, 0, strpos($fieldType, '('));

        switch ($type) {
            case 'int':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'tinyint':
                return intval($value);
            case 'date':
                $date = date_create($value);
                return date_format($date, "Y-m-d");
            case 'varchar':
                $length = $this->getTextBetweenParens($fieldType);
                return substr($value, $length);
            case 'char':
                $length = $this->getTextBetweenParens($fieldType);
                return substr($value, $length);
            default:
                return $value;
        }
    }

    private function getTextBetweenParens($string)
    {
        preg_match('#\((.*?)\)#', $string, $match);

        return $match[1];
    }

}