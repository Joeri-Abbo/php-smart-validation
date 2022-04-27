<?php

namespace JoeriAbbo\SmartValidation\Services;

use Illuminate\Support\Facades\DB;

class ValidationTableRepository
{
    private static ?ValidationTableRepository $instance = null;

    /**
     * Get class instance.
     * @return ValidationTableRepository
     */
    public static function getInstance(): ValidationTableRepository
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get validation for table
     * @param string|null $table
     * @return array
     */
    public function getValidationForTable(string $table = null): array
    {
        return $this->getValidationRulesForColumns(DB::select('show columns from ' . $table), $table);
    }

    /**
     * Get validation rules for columns
     * @param object $column
     * @param string $table
     * @return array
     */
    private function getValidationRulesForColumn(object $column, string $table): array
    {
        $column_rules = [];

        if ($column->Null == 'YES') {
            $column_rules[] = 'nullable';
        }
        if ($column->Key == 'PRI') {
            $column_rules[] = 'unique:' . $table;
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

        return $column_rules;
    }

    /**
     * Get validation for columns
     * @param array|null $columns
     * @param string $table
     * @return array
     */
    private function getValidationRulesForColumns(?array $columns, string$table): array
    {
        $rules = [];
        foreach ($columns as $column) {
            $rules[$column->Field] = $this->getValidationRulesForColumn($column, $table);
        }
        return $rules;
    }

    /**
     * Get string between specif character
     * @param string $string
     * @param string $start
     * @param string $end
     * @return string
     */
    function getStringBetween(string $string, string $start, string $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}