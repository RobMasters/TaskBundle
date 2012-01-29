<?php

namespace RobMasters\Bundle\TaskBundle\Event;

use \Symfony\Component\EventDispatcher\Event;
use \RobMasters\Bundle\TaskBundle\Task\TaskInterface;


class ExceptionEvent extends Event
{
    /**
     * @var \RobMasters\Bundle\TaskBundle\Event\TaskInterface
     */
    private $task;

    /**
     * @var \Exception
     */
    private $exception;

    function __construct(TaskInterface $task, \Exception $e)
    {
        $this->task = $task;
        $this->exception = $e;
    }

    /**
     * @return \RobMasters\Bundle\TaskBundle\Event\TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}