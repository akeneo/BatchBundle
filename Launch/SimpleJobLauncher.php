<?php

namespace Akeneo\Bundle\BatchBundle\Launch;

use Akeneo\Bundle\BatchBundle\Entity\JobExecution;
use Akeneo\Bundle\BatchBundle\Job\Job;
use Akeneo\Bundle\BatchBundle\Job\JobParameters;
use Akeneo\Bundle\BatchBundle\Job\JobRepositoryInterface;
use Akeneo\Bundle\BatchBundle\Launch\JobLauncherInterface;


/**
 * Class SimpleJobLauncher
 *
 * @author    Stephane Chapeau <stephane.chapeau@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SimpleJobLauncher implements JobLauncherInterface {

    private $jobRepository;

    private $taskExecutor;


    /**
     * Provides the doctrine entity manager
     * @param EntityManager $entityManager
     */
    public function __construct(JobRepositoryInterface $jobRepository, TaskExecutor $taskExecutor)
    {
        $this->jobRepository = $jobRepository;
        $this->taskExecutor = $taskExecutor;
    }

    /**
     * @param Job           $job
     * @param JobParameters $jobParameters
     *
     * @return JobExecution
     */
    public function run(Job $job, JobParameters $jobParameters)
    {
        //Assert.notNull($job, "The Job must not be null.");
        //Assert.notNull($jobParameters, "The JobParameters must not be null.");

        $job->getJobParametersValidator()->validate($jobParameters);

        $jobExecution = $this->jobRepository->createJobExecution($job->getName(), $jobParameters);

        try {
            $this->taskExecutor->execute($jobExecution);
		}
        catch (TaskRejectedException $e) {

            $jobExecution->upgradeStatus(BatchStatus::FAILED);

            if ($jobExecution->getExitStatus()==ExitStatus::UNKNOWN) {
                $jobExecution->setExitStatus(ExitStatus::FAILED); //.addExitDescription(e)
            }
            $this->jobRepository->update(jobExecution);
        }

		return jobExecution;
    }
}
