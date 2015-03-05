<?php

namespace Akeneo\Bundle\BatchBundle\Task;

use Akeneo\Bundle\BatchBundle\Task\TaskRejectedException;
use Akeneo\Bundle\BatchBundle\Entity\JobExecution;

/**
 * Interface TaskExecutor
 *
 * @author    Stephane Chapeau <stephane.chapeau@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface TaskExecutor {

    /**
     * Execute the given task
     *
     * The call might return immediately if the implementation uses
     * an asynchronous execution strategy, or might block in the case
     * of synchronous execution.
     *
     * @param $jobExecution
     * @throws TaskRejectedException if the given task was not accepted
     */
    function execute(JobExecution $jobExecution);
}
