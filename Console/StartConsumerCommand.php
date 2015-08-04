<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Amqp\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Amqp\ConsumerFactory;

/**
 * Command for starting AMQP consumers.
 */
class StartConsumerCommand extends Command
{
    const ARGUMENT_CONSUMER = 'consumer';
    const ARGUMENT_NUMBER_OF_MESSAGES = 'messages';
    const COMMAND_QUEUE_CONSUMERS_START = 'queue:consumers:start';

    /**
     * @var ConsumerFactory
     */
    private $consumerFactory;

    /**
     * {@inheritdoc}
     *
     * @param ConsumerFactory $consumerFactory
     */
    public function __construct(ConsumerFactory $consumerFactory, $name = null)
    {
        $this->consumerFactory = $consumerFactory;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumerName = $input->getArgument(self::ARGUMENT_CONSUMER);
        $numberOfMessages = $input->getArgument(self::ARGUMENT_NUMBER_OF_MESSAGES);
        $consumer = $this->consumerFactory->get($consumerName);
        $consumer->process($numberOfMessages);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_QUEUE_CONSUMERS_START);
        $this->setDescription('Start AMQP consumer.');
        $this->addArgument(
            self::ARGUMENT_CONSUMER,
            InputArgument::REQUIRED,
            'The name of the consumer to be started.'
        );
        $this->addArgument(
            self::ARGUMENT_NUMBER_OF_MESSAGES,
            InputArgument::OPTIONAL,
            'The number of messages to be processed by the consumer before process termination. '
            . 'If not specify - wait for new messages forever.'
        );
        $this->setHelp(
            <<<HELP
This command starts AMQP consumer by its name.

To start consumer which will wait for new messages forever:

      <comment>%command.full_name% --consumer=some_consumer</comment>

To specify the number of messages which should be processed by consumer before its termination:

    <comment>%command.full_name% --consumer=some_consumer --messages=50</comment>
HELP
        );
        parent::configure();
    }
}
