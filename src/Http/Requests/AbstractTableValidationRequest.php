<?php

namespace JoeriAbbo\SmartValidation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

abstract class AbstractTableValidationRequest extends FormRequest
{

    /**
     * Table to use for getting the validation rules
     * @return string
     */
    abstract public static function getTable(): string;

    /**
     * The basic rules for the request based on the given table
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $columns = DB::select('show columns from ' . static::getTable());
        foreach ($columns as $column) {
            $column_rules = [];

            if ($column->Null == 'YES') {
                $column_rules[] = 'nullable';
            }
            if ($column->Key == 'PRI') {
                $column_rules[] = 'unique:' . static::getTable();
            }

            if (!empty($max_number = $this->getStringBetween($column->Type, '(', ')'))) {
                $column_rules[] = 'max:' . $max_number;
            }
            switch (strtok($column->Type, '(')) {
                case 'char':
                case 'tinytext':
                case 'longtext':
                case 'mediumtext':
                case 'text':
                case 'varchar':
                case 'datetime':
                case 'timestamp':
                case 'date':
                    $column_rules[] = 'date';
                    break;
                case 'int':
                case 'smallint':
                case 'mediumint':
                case 'year':
                case 'bigint':
                    $column_rules[] = 'integer';
                    break;
                case 'float':
                case 'double':
                    $column_rules[] = 'numeric';
                    break;
                case 'decimal':
                    $column_rules[] = 'decimal';
                    break;
                case 'bit':
                    $column_rules[] = 'min:1';
                    $column_rules[] = 'max:1';
                    break;
                case 'tinyint':
                    $column_rules[] = 'boolean';
                    break;
                default:
                    break;
            }
            $rules[$column->Field] = $column_rules;
            unset($column_rules);
        }

        return $rules;
    }

    function getStringBetween(string $string, string $start, string $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}