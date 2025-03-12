<?php
namespace Dolphin\CronGrid\Model;

use Magento\Framework\Shell;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Process\PhpExecutableFinderFactory;

/**
 * Class Command
 *
 * Command class for executing cron commands via shell.
 */
class Command
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var PhpExecutableFinderFactory
     */
    private $phpExecutableFinder;

    /**
     * Command constructor.
     *
     * @param Shell $shell
     * @param PhpExecutableFinderFactory $phpExecutableFinderFactory
     */
    public function __construct(
        Shell $shell,
        PhpExecutableFinderFactory $phpExecutableFinderFactory
    ) {
        $this->shell = $shell;
        $this->phpExecutableFinder = $phpExecutableFinderFactory->create();
    }

    /**
     * Run the cron command.
     *
     * @param string $command The command to run, default is 'cron:run'.
     * @throws LocalizedException If the command cannot be executed.
     */
    public function run($command = 'cron:run')
    {
        $phpPath = $this->phpExecutableFinder->find() ?: 'php';
        $this->shell->execute('%s %s %s', [$phpPath, BP . '/bin/magento', $command]);
    }
}
