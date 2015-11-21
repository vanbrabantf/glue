<?php
namespace Madewithlove\Nanoframework\Console\Commands;

use Interop\Container\ContainerInterface;
use Psy\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TinkerCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * TinkerCommand constructor.
     *
     * @param $container
     * @param $shell
     */
    public function __construct(ContainerInterface $container, Shell $shell)
    {
        parent::__construct('tinker');

        $this->container = $container;
        $this->shell     = $shell;
    }

    protected function configure()
    {
        $this->setDescription('Tinker with the application and its classes');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->shell->setScopeVariables([
            'app' => $this->container,
        ]);

        $this->shell->run();
    }
}
