<?php

namespace Stuart\Validation;

class Job
{

    public function validate($job)
    {
        $errors = array();

        $dropoffAtCount = array_reduce($job->getDropoffs(), function ($counter, $dropoff) {
            return $counter + ($dropoff->getDropoffAt() === null ? 0 : 1);
        }, 0);


        if ($dropoffAtCount > 0 && count($job->getDropoffs()) > 1) {
            $errors[] = new Error('DROPOFF_AT_CAN_BE_USED_WITH_ONLY_ONE_DROPOFF');
        }

        return $errors;
    }
 }
