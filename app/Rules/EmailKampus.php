<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailKampus implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! str_ends_with(strtolower((string) $value), '@student.ub.ac.id')) {
            $fail('Gunakan email mahasiswa UB (berakhiran @student.ub.ac.id).');
        }
    }
}
