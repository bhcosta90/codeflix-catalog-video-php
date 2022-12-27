<?php

namespace Tests\Unit\Costa\DomainPackage\Domain\Validation;

use Costa\DomainPackage\Domain\Entity\Exception\EntityValidationException;
use Costa\DomainPackage\Domain\Validation\DomainValidation;
use Tests\Unit\TestCase;
use Throwable;

class DomainValidationTest extends TestCase
{
    public function testNotNull()
    {
        try {
            $value = '';
            DomainValidation::notNull($value);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testNotNullCustomMessageException()
    {
        try {
            $value = '';
            DomainValidation::notNull($value, 'custom message error');

            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'custom message error');
        }
    }

    public function testStrMaxLength()
    {
        try {
            $value = 'Test';
            DomainValidation::strMaxLength($value, 3, 'Custom Message');

            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom Message');
        }
    }

    public function testStrMinLength()
    {
        try {
            $value = 'Test';
            DomainValidation::strMinLength($value, 8, 'Custom Message');

            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom Message');
        }
    }

    public function testStrCanNullAndMaxLength()
    {
        try {
            $value = 'Test';
            DomainValidation::strCanNullAndMaxLength($value, 3, 'Custom Message');

            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom Message');
        }
    }
}
