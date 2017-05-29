<?php

namespace Messagebird\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Messagebird\Model\Mode;

class SetModeCommand extends Command
{
    protected function configure()
    {
        $this->setName('message:set-mode')
            ->addArgument('mode', InputArgument::REQUIRED, 'The mode to be set')
            ->setDescription('Changes system mode.')
            ->setHelp('This commands lets to choose between test and prod modes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modeModel = new Mode();
        $mode = filter_var($input->getArgument('mode'), FILTER_SANITIZE_STRING);
        $modeModel->setMode($mode);
        $output->writeln('Mode set to ' . $mode);
    }
}