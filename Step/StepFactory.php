<?php

namespace Akeneo\Bundle\BatchBundle\Step;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Util\Inflector;
use Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository;

/**
 * Step instance factory
 *
 * @author    Gildas Quemener <gildas.quemener@gmail.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class StepFactory
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var DoctrineJobRepository */
    protected $jobRepository;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param DoctrineJobRepository    $jobRepository
     */
    public function __construct($eventDispatcher, $jobRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->jobRepository   = $jobRepository;
    }

    /**
     * @param string $name
     * @param string $class
     * @param array  $services
     * @param array  $parameters
     *
     * @return StepInterface
     */
    public function createStep($name, $class, array $services, array $parameters)
    {
        if ('Akeneo\Bundle\BatchBundle\Step\ItemStep' === $class) {
            $reader = $services['reader'];
            $processor = $services['processor'];
            $writer = $services['writer'];
            $step = new ItemStep(
                $this->jobRepository,
                $this->eventDispatcher,
                $reader,
                $processor,
                $writer,
                $name
            );
        } else {
            $step = new $class($name);
            $step->setEventDispatcher($this->eventDispatcher);
            $step->setJobRepository($this->jobRepository);

            foreach ($services as $setter => $service) {
                $method = 'set'.Inflector::camelize($setter);
                $step->$method($service);
            }
        }

        foreach ($parameters as $setter => $param) {
            $method = 'set'.Inflector::camelize($setter);
            $step->$method($param);
        }

        return $step;
    }
}
