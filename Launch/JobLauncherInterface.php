<?php

namespace Akeneo\Bundle\BatchBundle\Launch;

use Akeneo\Bundle\BatchBundle\Job\Job;
use Akeneo\Bundle\BatchBundle\Job\JobParameters;
use Akeneo\Bundle\BatchBundle\Entity\JobExecution;

/**
 * Interface JobLauncherInterface
 *
 * @author    Stephane Chapeau <stephane.chapeau@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface JobLauncherInterface {

    /**
     * @param Job           $job
     * @param JobParameters $jobParameters
     *
     * @return JobExecution
     */
    public function run(Job $job, JobParameters $jobParameters);

}
