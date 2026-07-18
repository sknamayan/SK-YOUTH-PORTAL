<?php

namespace App\Traits;

trait EnforcesUppercaseInputs
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (property_exists($this, 'uppercaseFields') && is_array($this->uppercaseFields)) {
            $input = $this->all();

            foreach ($this->uppercaseFields as $field) {
                if ($this->has($field)) {
                    $value = $this->input($field);
                    if (is_string($value)) {
                        $input[$field] = mb_strtoupper($value, 'UTF-8');
                    }
                }
            }

            $this->merge($input);
        }
    }
}
