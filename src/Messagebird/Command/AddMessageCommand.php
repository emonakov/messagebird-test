<?php

namespace Messagebird\Command;

use Messagebird\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Messagebird\Model\ModelFactory;
use Messagebird\Model\Message;
use Messagebird\Model\Queue;

class AddMessageCommand extends Command
{
    protected function configure()
    {
        $this->setName('message:add')
            ->addArgument('message', InputArgument::REQUIRED, 'The message to be sent')
            ->setDescription('Adds new message to system.')
            ->setHelp('This command allows you to add a message...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Message $messageModel */
        $messageModel = ModelFactory::create('\Messagebird\Model\Message');
        $message = filter_var($input->getArgument('message'), FILTER_SANITIZE_STRING);
        $messageModel->setMessage($message);
        $messageModel->setText($message);
        $messageModel->save();
        $output->writeln('Message added');
    }
}