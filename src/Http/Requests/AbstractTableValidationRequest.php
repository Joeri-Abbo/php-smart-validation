<?php

namespace JoeriAbbo\SmartValidation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JoeriAbbo\SmartValidation\Services\ValidationTableRepository;

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
        return ValidationTableRepository::getInstance()->getValidationForTable(static::getTable());
    }

}