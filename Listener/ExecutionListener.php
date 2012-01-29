<?php

namespace RobMasters\Bundle\TaskBundle\Listener;

use RobMasters\Bundle\TaskBundle\Task\Task;
use RobMasters\Bundle\TaskBundle\Task\CompositeTask;
use RobMasters\Bundle\TaskBundle\Event\TaskEvent;
use RobMasters\Bundle\TaskBundle\Event\SubTaskEvent;
use RobMasters\Bundle\TaskBundle\Exception\TaskException;

class ExecutionListener extends BaseListener
{
    private $startFromTask = null;

    private $startingTaskReached = true;

    private $specifiedTasks = array();

    private $excludedTasks = array();


    /**
     * Specify one or more tasks to be run exclusively - any that don't match will be skipped
     *
     * @param   array|string $tasks
     * @throws  \InvalidArgumentException|\LogicException
     */
    public function only($tasks)
    {
        $tasks = (array) $tasks;
        foreach($tasks as $taskName) {
            if(!is_string($taskName)) {
                throw new \InvalidArgumentException('The "only" method must be given either a task name, or an array of task names.');
            }

            if(in_array($taskName, $this->excludedTasks)) {
                throw new \LogicException("Cannot specify task, $taskName, to be executed as it has already been excluded");
            }

            $this->specifiedTasks[] = $taskName;
        }
    }

    /**
     * Specify one or more tasks to exclude from execution
     *
     * @param   array|string $tasks
     * @throws  \InvalidArgumentException|\LogicException
     */
    public function except($tasks)
    {
        $tasks = (array) $tasks;
        foreach($tasks as $taskName) {
            if(!is_string($taskName)) {
                throw new \InvalidArgumentException('The "except" method must be given either a task name, or an array of task names.');
            }

            if(in_array($taskName, $this->specifiedTasks)) {
                throw new \LogicException("Cannot exclude task, $taskName, as it has already been added to the exclusive list of tasks to execute");
            }

            $this->specifiedTasks[] = $taskName;
        }
    }

    /**
     * Specify the name of a task to begin execution at. Any tasks before this will be skipped.
     *
     * @param $taskName
     * @throws \InvalidArgumentException
     */
    public function startFrom($taskName)
    {
        if(is_array($taskName)) {
            throw new \InvalidArgumentException('Cannot specify multiple tasks to start from - only the name of a single task');
        }

        $this->startFromTask = $taskName;
        $this->startingTaskReached = false;
    }

    /**
     * Check if a task should be processed based on how the listener has been configured
     *
     * @param \RobMasters\Bundle\TaskBundle\Event\TaskEvent $event
     * @return bool
     */
    public function onStart(TaskEvent $event)
    {
        $task = $event->getTask();
        $taskName = $task->getName();

        if(!$task instanceof Task) {
            // Can't skip tasks that implement the interface directly without extending the base Task
            return true;
        }

        // Specified task/starting point exclusions do not apply to composites otherwise it wouldn't be possible to
        // start from or only execute a specific sub-task
        if(!$task instanceof CompositeTask) {
            if(!$this->startingTaskReached && $taskName !== $this->startFromTask) {
                $task->skip();
                return false;
            }

            if(!empty($this->specifiedTasks) && !in_array($taskName, $this->specifiedTasks)) {
                $task->skip();
                return false;
            }
        }

        // Exclude the task if necessary, regardless of whether it is a composite or not
        if(!empty($this->excludedTasks) && in_array($taskName, $this->excludedTasks)) {
            $task->skip();
            return false;
        }

        return true;
    }

    /**
     * If a composite task has been explicitly requested to execute, then all of it's sub-tasks must execute as well
     *
     * The onStartSubTask event is called before the sub-task's own onStart event, so we can simply append
     * it's name to the specified list of tasks to execute. Unless, of course, the sub-task has been explicitly ignored.
     *
     * @param \RobMasters\Bundle\TaskBundle\Event\SubTaskEvent $event
     */
    public function onStartSubTask(SubTaskEvent $event)
    {
        $compositeTaskName = $event->getCompositeTask()->getName();
        $subTaskName = $event->getSubTask()->getName();

        if(in_array($compositeTaskName, $this->specifiedTasks) && !in_array($subTaskName, $this->excludedTasks)) {
            $this->specifiedTasks[] = $event->getSubTask()->getName();
        }
    }

}