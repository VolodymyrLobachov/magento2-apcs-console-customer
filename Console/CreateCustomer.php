<?php

declare(strict_types=1);

namespace APCS\ConsoleCustomer\Console;

use APCS\BaseLogger\Logger\Logger;
use APCS\ConsoleCustomer\Model\CustomerCreator;
use APCS\ConsoleCustomer\Service\Validator;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use APCS\ConsoleCustomer\Api\ConsoleCustomerInterface;

class CreateCustomer extends Command
{
    /**
     * @var CustomerCreator
     */
    private CustomerCreator $customerCreator;

    /**
     * @var Logger
     */
    private Logger $apcsLogger;

    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param CustomerCreator $customerCreator
     * @param Logger $apcsLogger
     * @param Validator $validator
     */
    public function __construct(
        CustomerCreator $customerCreator,
        Logger $apcsLogger,
        Validator $validator
    ) {
        $this->customerCreator = $customerCreator;
        $this->apcsLogger = $apcsLogger;
        $this->validator = $validator;
        parent::__construct();
    }

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('apcs:create:customer');
        $this->setDescription('This command create new Customer via console');
        $this->addOption(
            ConsoleCustomerInterface::FIRSTNAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Customer firstname'
        );
        $this->addOption(
            ConsoleCustomerInterface::LASTNAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Customer lastname'
        );
        $this->addOption(
            ConsoleCustomerInterface::EMAIL,
            null,
            InputOption::VALUE_REQUIRED,
            'Customer email'
        );
        $this->addOption(
            ConsoleCustomerInterface::PASSWORD,
            null,
            InputOption::VALUE_REQUIRED,
            'Customer password'
        );
        $this->addOption(
            ConsoleCustomerInterface::WEBSITE,
            null,
            InputOption::VALUE_REQUIRED,
            'Website'
        );
        $this->addOption(
            ConsoleCustomerInterface::SHOW_ALL_CUSTOMER_DATA,
            null,
            InputOption::VALUE_OPTIONAL,
            'Show all customer data in console',
            0
        );
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->validator->validate($input->getOptions());

            $customer = $this->customerCreator->execute([
                'firstname' => $input->getOption(ConsoleCustomerInterface::FIRSTNAME),
                'lastname' => $input->getOption(ConsoleCustomerInterface::LASTNAME),
                'email' => $input->getOption(ConsoleCustomerInterface::EMAIL),
                'password' => $input->getOption(ConsoleCustomerInterface::PASSWORD)
            ]);

            if ($input->getOption(ConsoleCustomerInterface::SHOW_ALL_CUSTOMER_DATA)) {
                foreach ($customer->getData() as $attName => $value) {
                    $output->writeln('<comment>Customer Attribute ' . $attName . ' : ' . $value . '</comment>');
                }
            }

            $output->writeln('<info>Customer ids`' . $customer->getId() . '`</info>');
            $output->writeln('<info>Customer providet Name`' . $input->getOption(ConsoleCustomerInterface::FIRSTNAME) . '`</info>');
            $output->writeln('<info>Customer providet Last Name`' . $input->getOption(ConsoleCustomerInterface::LASTNAME) . '`</info>');
            $output->writeln('<info>Customer providet Email`' . $input->getOption(ConsoleCustomerInterface::EMAIL) . '`</info>');
            $output->writeln('<info>Customer WebsiteId`' . $customer->getWebsiteId() . '`</info>');
            $output->writeln('<info>Customer providet Password`' . $input->getOption(ConsoleCustomerInterface::PASSWORD) . '`</info>');

            $output->writeln('<info>**********************************</info>');
            $output->writeln('<info>The customer successfully created!</info>');
            $output->writeln('<info>**********************************</info>');

            return ConsoleCustomerInterface::SUCCESS_CODE;
        } catch (LocalizedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $this->apcsLogger->error($e->getMessage());

            return ConsoleCustomerInterface::FAILURE_CODE;
        }
    }
}
