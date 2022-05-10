<?php
/**
 * @author Hipex <info@hipex.io>
 * @copyright (c) Hipex B.V. 2018
 */

namespace HipexDeploy\Deployer\Task;

use HipexDeploy\Stdlib\ClassFinder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class TaskFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * TaskFactory constructor.
     *
     * @param ContainerInterface $container
     * @param LoggerInterface $log
     */
    public function __construct(ContainerInterface $container, LoggerInterface $log)
    {
        $this->container = $container;
        $this->log = $log;
    }

    /**
     * Load all tasks
     *
     * @return TaskInterface[]
     */
    public function loadAll(): array
    {
        $classFinder = new ClassFinder(__NAMESPACE__);
        $classFinder->implements(TaskInterface::class);
        $classFinder->in(__DIR__);

        $tasks = [];
        foreach ($classFinder as $class) {
            if (!$this->container->has($class)) {
                $this->log->error(sprintf('Found task %s but di can not configure', $class));
                continue;
            }

            $task = $this->container->get($class);
            if (!$task instanceof TaskInterface) {
                $this->log->error(sprintf('Found task %s but does implement %s', $class, TaskInterface::class));
                continue;
            }

            $tasks[] = $task;
        }

        return $tasks;
    }
}