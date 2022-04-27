<?php

namespace JoeriAbbo\SmartValidation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractTableValidationRequest extends FormRequest
{

    abstract public function getTable(): string;

    public function rules(): array
    {
        return [];
    }
}