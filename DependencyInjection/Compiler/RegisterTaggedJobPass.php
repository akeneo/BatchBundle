<?php

namespace Akeneo\Bundle\BatchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register tagged jobs (replace the old batch_jobs.yml system)
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class RegisterTaggedJobPass implements CompilerPassInterface
{
    /** @staticvar */
    const JOB_REGISTRY = 'akeneo_batch.connectors';

    /** @staticvar */
    const JOB_TAG = 'akeneo_batch.job';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerJobs($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerJobs(ContainerBuilder $container)
    {
        $registry = $container->getDefinition(self::JOB_REGISTRY);
        $jobs = $container->findTaggedServiceIds(self::JOB_TAG);

        foreach ($jobs as $jobId => $attributes) {
            $attributes = current($attributes);
            $jobAlias = $attributes['job_alias'];
            $jobType = $attributes['job_type'];
            $jobConnector = $attributes['connector'];

            $registry->addMethodCall('register', [new Reference($jobId), $jobAlias, $jobType, $jobConnector]);
        }
    }
}
