<?php

namespace Akeneo\Bundle\BatchBundle\Tests\Unit\EventListener;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Akeneo\Bundle\BatchBundle\EventListener\PushStdOutLoggerHandlerListener;
use Akeneo\Bundle\BatchBundle\Command\BatchCommand;

/**
 * Test related class
 */
class PushStdOutLoggerHandlerListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;
    protected $subscriber;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();
        $this->subscriber = new PushStdOutLoggerHandlerListener($this->logger);
    }

    public function testPush()
    {
        $command = new BatchCommand();
        $input = new ArrayInput([]);
        $output = new NullOutput();
        $event = new ConsoleCommandEvent($command, $input, $output);

        $this
            ->logger
            ->expects($this->once())
            ->method('pushHandler')
            ->with($this->callback(function ($subject) {
                return $subject instanceof \Monolog\Handler\StreamHandler;
            }));

        $this->subscriber->push($event);
    }
}
