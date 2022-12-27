<?php

namespace Costa\DomainPackage\Domain\Validation;

use Costa\DomainPackage\Domain\Entity\Exception\EntityValidationException;

class DomainValidation
{
    public static function notNull(string $value, string $exceptMessage = null)
    {
        if (empty($value)) {
            throw new EntityValidationException($exceptMessage ?? 'Should not be empty or null');
        }
    }

    public static function strMaxLength(string $value, int $length = 255, string $exceptMessage = null)
    {
        if (strlen($value ?: "") >= $length) {
            $message = $exceptMessage ?? "The value must not be greater than {$length} characters";
            throw new EntityValidationException($message);
        }
    }

    public static function strMinLength(string $value, int $length = 3, string $exceptMessage = null)
    {
        if (strlen($value ?: "") < $length) {
            throw new EntityValidationException($exceptMessage ?? "The value must be at least {$length} characters");
        }
    }

    public static function strCanNullAndMaxLength(string $value = null, int $length = 255, string $exceptMessage = null)
    {
        if (!empty($value) && strlen($value ?: "") > $length) {
            $message = $exceptMessage ?? "The value must not be greater than {$length} characters";
            throw new EntityValidationException($message);
        }
    }
}
