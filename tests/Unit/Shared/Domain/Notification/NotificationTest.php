<?php

namespace Tests\Unit\Costa\DomainPackage\Domain\Notification;

use Costa\DomainPackage\Domain\Notification\Notification;
use Costa\DomainPackage\Domain\Notification\DTO\Input;

use Tests\Unit\TestCase;

class NotificationTest extends TestCase
{
    public function testGetErrors()
    {
        $notification = new Notification();
        $errors = $notification->getErrors();
        $this->assertIsArray($errors);
    }

    public function testAddErrors()
    {
        $notification = new Notification();
        $notification->addError(new Input(
            context: 'video',
            message: 'test'
        ));
        $errors = $notification->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testAsErrors()
    {
        $notification = new Notification();
        $this->assertFalse($notification->hasErrors());
        $notification->addError(new Input(
            context: 'video',
            message: 'test'
        ));
        $this->assertTrue($notification->hasErrors());
    }

    public function testMessage()
    {
        $notification = new Notification();
        $notification->addError(new Input(
            context: 'video',
            message: 'test'
        ));
        $notification->addError(new Input(
            context: 'video',
            message: 'test 2'
        ));
        $notification->addError(new Input(
            context: 'video 2',
            message: 'test 2'
        ));
        $this->assertEquals(
            'video: test, test 2 | video 2: test 2',
            $notification->message()
        );
    }

    public function testMessageFilterContext()
    {
        $notification = new Notification();
        $notification->addError(new Input(
            context: 'video',
            message: 'test'
        ));
        $notification->addError(new Input(
            context: 'video',
            message: 'test 2'
        ));
        $notification->addError(new Input(
            context: 'video 2',
            message: 'test 2'
        ));
        $this->assertEquals(
            'video 2: test 2',
            $notification->message('video 2')
        );
    }
}
