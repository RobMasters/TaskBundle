<?php

namespace RobMasters\Bundle\TaskBundle\Tests\Task;

use RobMasters\Bundle\TaskBundle\Task\Task;
use RobMasters\Bundle\TaskBundle\Exception\TaskException;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RobMasters\Bundle\TaskBundle\Task\Task
     */
    private $task;

    protected function setUp()
    {
        parent::setUp();

        $this->task = $this->getMockTask();
    }

    /**
     * Test that a task is successfully processed when not instructed otherwise
     */
    public function testTaskIsProcessedByDefault()
    {
        $this->task->expects($this->once())
            ->method('doProcess')
            ->will($this->returnValue(true));

        $processed = $this->task->process();
        $this->assertTrue($processed);
    }

    /**
     * Test that a task may be explicitly skipped - preventing any call to it's doProcess() method
     */
    public function testTaskCanBeSkipped()
    {
        $this->task->skip();

        $this->task->expects($this->never())
            ->method('doProcess');

        $this->task->process();
    }

    /**
     * Test that dispatch is called on the event dispatcher when passed to the task
     */
    public function testEventIsDispatchedWhenTaskHasDispatcher()
    {
        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->atLeastOnce())
            ->method('dispatch')
            ->will($this->returnArgument(1));

        $this->task->setDispatcher($dispatcher);

        $this->task->process();
    }

    /**
     * Test that exceptions to do with the processing of the task are caught and handled, returning a false value
     */
    public function testTaskHandlesExceptionsGracefully()
    {
        $this->task->expects($this->once())
            ->method('doProcess')
            ->will($this->throwException(new \Exception('This will be caught and dispatched as an error event')));

        $processed = $this->task->process();

        $this->assertFalse($processed);
    }

    /**
     * Test that exceptions to do with the task logic itself are not handled internally
     */
    public function testTaskFailsOnTaskExceptions()
    {
        $this->task->expects($this->once())
            ->method('doProcess')
            ->will($this->throwException(new TaskException('This will be prevent any further task execution')));

        $this->setExpectedException('RobMasters\Bundle\TaskBundle\Exception\TaskException');
        $this->task->process();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RobMasters\Bundle\TaskBundle\Task\Task
     */
    private function getMockTask()
    {
        $task = $this->getMockForAbstractClass('RobMasters\Bundle\TaskBundle\Task\Task', array(), '', true, true, true,
            array( 'getName')
        );
        $task->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mock_task'));

        return $task;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\EventDispatcher\EventDispatcher
     */
    private function getMockDispatcher()
    {
        $dispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcher', array('dispatch'));

        return $dispatcher;
    }

}