<?php

namespace RobMasters\Bundle\TaskBundle\Task;

interface TaskInterface
{
    /**
     * @abstract
     * @return string
     */
    public function getName();

    /**
     * @abstract
     * @return boolean
     */
    public function process();
}
 
