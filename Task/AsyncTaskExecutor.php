<?php

namespace Akeneo\Bundle\BatchBundle\Task;

use Akeneo\Bundle\BatchBundle\Entity\JobExecution;

/**
 * Class AsyncTaskExecutor
 *
 * @author    Stephane Chapeau <stephane.chapeau@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AsyncTaskExecutor implements TaskExecutor {

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $environment;

    /** @var OperatorRegistry */
    protected $operatorRegistry;

    /** @var NotificationManager */
    protected $notificationManager;

    /**
     * Constructor
     *
     * @param ObjectManager            $objectManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param OperatorRegistry         $operatorRegistry
     * @param NotificationManager      $notificationManager
     * @param string                   $jobExecutionClass
     * @param string                   $rootDir
     * @param string                   $environment
     */
    public function __construct(
        ObjectManager $objectManager,
        EventDispatcherInterface $eventDispatcher,
        OperatorRegistry $operatorRegistry,
        NotificationManager $notificationManager,
        $jobExecutionClass,
        $rootDir,
        $environment
    ) {
        parent::__construct($objectManager, $eventDispatcher, $jobExecutionClass);

        $this->operatorRegistry    = $operatorRegistry;
        $this->rootDir             = $rootDir;
        $this->environment         = $environment;
        $this->notificationManager = $notificationManager;
    }

    /**
     * @Override
     */
    function execute(JobExecution $jobExecution)
    {
        $instanceCode = $jobExecution->getJobInstance()->getCode();
        $executionId  = $jobExecution->getId();
        $pathFinder  = new PhpExecutableFinder();

        $rawConfiguration = $jobExecution->getJobParameters();

        $cmd = sprintf(
            '%s %s/console akeneo:batch:job --env=%s %s --config=\'[%s]\' >> %s/logs/batch_execute.log 2>&1',
            $pathFinder->find(),
            $this->rootDir,
            $this->environment,
            $executionId,
            $rawConfiguration,
            $this->rootDir
        );
        // Please note we do not use Symfony Process as it has some problem
        // when executed from HTTP request that stop fast (race condition that makes
        // the process cloning fail when the parent process, i.e. HTTP request, stops
        // at the same time)
        exec($cmd . ' &');

        $this->eventDispatcher->dispatch(JobProfileEvents::POST_EXECUTE, new GenericEvent($jobExecution->getJobInstance()));

        return $jobExecution;
    }
}
