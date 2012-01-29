<?php

namespace RobMasters\Bundle\TaskBundle\Event;

use \Symfony\Component\EventDispatcher\Event;
use \RobMasters\Bundle\TaskBundle\Task\TaskInterface;


class TaskEvent extends Event
{
    /**
     * @var \RobMasters\Bundle\TaskBundle\Event\TaskInterface
     */
    private $task;

    function __construct( TaskInterface $task )
    {
        $this->task = $task;
    }

    /**
     * @return \RobMasters\Bundle\TaskBundle\Task\TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }
}