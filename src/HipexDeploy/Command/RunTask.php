<?php
/**
 * @author Hipex <info@hipex.io>
 * @copyright (c) Hipex B.V. 2019
 */

namespace HipexDeploy\Command;

use HipexDeploy\DeployRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class RunTask extends Command
{
    /**
     * @var DeployRunner
     */
    private $deployRunner;

    public function __construct(DeployRunner $deployRunner)
    {
        parent::__construct();
        $this->deployRunner = $deployRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('run-task');
        $this->setDescription('Run a seperate deployer task');
        $this->addArgument('stage', InputArgument::REQUIRED, 'Stage');
        $this->addArgument('task', InputArgument::REQUIRED, 'Task to run');
    }

    /**
     * {@inheritdoc}
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->deployRunner->run($output, $input->getArgument('stage'), $input->getArgument('task'));
    }
}