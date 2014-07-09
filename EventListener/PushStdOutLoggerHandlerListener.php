<?php

namespace Akeneo\Bundle\BatchBundle\EventListener;

use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Console\Input\InputInterface;

/**
 * EventListener/Firewall
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PushStdOutLoggerHandlerListener
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::COMMAND => ['push', 128],
        );
    }

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
