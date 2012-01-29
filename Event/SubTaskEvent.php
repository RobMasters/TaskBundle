<?php

namespace RobMasters\Bundle\TaskBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use RobMasters\Bundle\TaskBundle\Task\TaskInterface;
use RobMasters\Bundle\TaskBundle\Task\CompositeTask;


class SubTaskEvent extends Event
{
    /**
     * The composite task that the sub-task belongs to
     *
     * @var \RobMasters\Bundle\TaskBundle\Event\TaskInterface
     */
    private $compositeTask;

    /**
     * @var \RobMasters\Bundle\TaskBundle\Task\TaskInterface
     */
    private $subTask;


    /**
     * @param \RobMasters\Bundle\TaskBundle\Task\CompositeTask $compositeTask
     * @param \RobMasters\Bundle\TaskBundle\Task\TaskInterface $subTask
     */
    function __construct(CompositeTask $compositeTask, TaskInterface $subTask)
    {
        $this->compositeTask = $compositeTask;
        $this->subTask = $subTask;
    }

    /**
     * @return \RobMasters\Bundle\TaskBundle\Task\CompositeTask
     */
    public function getCompositeTask()
    {
        return $this->compositeTask;
    }

     /**
     * @return \RobMasters\Bundle\TaskBundle\Task\TaskInterface
     */
    public function getSubTask()
    {
        return $this->subTask;
    }
}