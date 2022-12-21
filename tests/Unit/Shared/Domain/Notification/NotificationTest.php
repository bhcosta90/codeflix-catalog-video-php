<?php

namespace Tests\Unit\Shared\Domain\Notification;

use Shared\Domain\Notification\Notification;
use Shared\Domain\Notification\DTO\Input;

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
        $notification->addErrors(new Input(
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
        $notification->addErrors(new Input(
            context: 'video',
            message: 'test'
        ));
        $this->assertTrue($notification->hasErrors());
    }

    public function testMessage()
    {
        $notification = new Notification();
        $notification->addErrors(new Input(
            context: 'video',
            message: 'test'
        ));
        $notification->addErrors(new Input(
            context: 'video',
            message: 'test 2'
        ));
        $notification->addErrors(new Input(
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
        $notification->addErrors(new Input(
            context: 'video',
            message: 'test'
        ));
        $notification->addErrors(new Input(
            context: 'video',
            message: 'test 2'
        ));
        $notification->addErrors(new Input(
            context: 'video 2',
            message: 'test 2'
        ));
        $this->assertEquals(
            'video 2: test 2',
            $notification->message('video 2')
        );
    }
}
