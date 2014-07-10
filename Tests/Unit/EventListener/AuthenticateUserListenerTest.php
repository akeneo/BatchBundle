<?php

namespace Akeneo\Bundle\BatchBundle\Tests\Unit\EventListener;

use Akeneo\Bundle\BatchBundle\EventListener\AuthenticateUserListener;
use Akeneo\Bundle\BatchBundle\Command\BatchCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Test related class
 */
class AuthenticateUserListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;
    protected $translator;
    protected $subscriber;

    protected function setUp()
    {
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->userProvider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');

        $this->subscriber = new AuthenticateUserListener(
            $this->securityContext,
            $this->logger,
            $this->userProvider
        );
    }

    public function testAuthentication()
    {
        $command = new BatchCommand();
        $input = new ArrayInput(array('--user' => 'admin'));
        $output = new NullOutput();
        $event = new ConsoleCommandEvent($command, $input, $output);

        $user = $this->getUserMock();
        $this
            ->userProvider
            ->expects($this->any())
            ->method('loadUserByUsername')
            ->with('admin')
            ->will($this->returnValue($user));

        $this
            ->securityContext
            ->expects($this->once())
            ->method('setToken')
            ->with($this->callback(function ($subject) use ($user) {
                return
                    $subject instanceof \Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken
                    && $user === $subject->getUser()
                    && null === $subject->getCredentials()
                    && 'batch' === $subject->getProviderKey()
                ;
            }));

        $this->subscriber->authenticate($event);
    }

    public function getUserMock()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())->method('getRoles')->will($this->returnValue(['foo']));

        return $user;
    }
}
