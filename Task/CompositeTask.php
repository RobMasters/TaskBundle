<?php

namespace RobMasters\Bundle\TaskBundle\Task;

use RobMasters\Bundle\TaskBundle\TaskEvents;
use RobMasters\Bundle\TaskBundle\Event\SubTaskEvent;
use RobMasters\Bundle\TaskBundle\Exception\TaskException;

/**
 *
 *
 * @author Rob Masters <rob@enginepowered.co.uk>
 */
class CompositeTask extends Task
{
    /**
     * @var TaskInterface[]
     */
    private $tasks = array();

    /**
     * Flag to determine whether sub-tasks should continue to be processed after one has failed
     *
     * Defaults to true
     *
     * @var bool
     */
    private $haltOnFailure = true;


    public function add(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $haltOnFailure
     */
    public function setHaltOnFailure($haltOnFailure)
    {
        $this->haltOnFailure = (boolean) $haltOnFailure;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    final protected function doProcess()
    {
        $succeeded = true;
        $this->configure();

        if(empty($this->tasks)) {
            throw new TaskException('A composite task must contain at least one task');
        }

        foreach($this->tasks as $subTask) {
            if($subTask instanceof Task) {
                // Pass on the event dispatcher, unless the sub-task just directly implements the task interface
                $subTask->setDispatcher($this->dispatcher);
            }

            $event = new SubTaskEvent($this, $subTask);
            $this->dispatcher->dispatch(TaskEvents::START_SUBTASK, $event);

            // Only return true if all sub-tasks have succeeded
            $succeeded = ($subTask->process() && $succeeded);

            if(!$succeeded && $this->haltOnFailure) {
                $event = new SubTaskEvent($this, $subTask);
                $this->dispatcher->dispatch(TaskEvents::HALT, $event);
            }
        }

        return $succeeded;
    }
}
