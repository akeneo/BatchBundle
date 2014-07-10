<?php

namespace Akeneo\Bundle\BatchBundle\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Monolog\Handler\StreamHandler;
use Monolog;
use Akeneo\Bundle\BatchBundle\Command\BatchCommand;

/**
 * Print batch command logger output to std out
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class PushStdOutLoggerHandlerListener
{
    /** @var Monolog\Logger */
    protected $logger;

    /**
     * @param Monolog\Logger $logger
     */
    public function __construct(Monolog\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Push a stream handler into the batch logger
     *
     * @param ConsoleCommandEvent $event
     */
    public function push(ConsoleCommandEvent $event)
    {
        if (!$event->getCommand() instanceof BatchCommand) {
            return;
        }

        $input = $event->getInput();

        if (!$input->hasParameterOption('--no-debug')) {
            $this->logger->pushHandler(new StreamHandler('php://stdout'));
        }
    }
}
