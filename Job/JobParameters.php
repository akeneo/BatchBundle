<?php

namespace Akeneo\Bundle\BatchBundle\Job;

/**
 * Class JobParameters
 *
 * @author    Stephane Chapeau <stephane.chapeau@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @ORM\Embeddable
 */
class JobParameters {

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $jobParameters = array();

    /**
     * @return array
     */
    public function getJobParameters()
    {
        return $this->jobParameters;
    }

    /**
     * @param array : parameters
     */
    public function setJobParameters($array)
    {
        $this->jobParameters = $array;
    }
}
