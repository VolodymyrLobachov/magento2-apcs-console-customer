<?php

declare(strict_types=1);

namespace APCS\ConsoleCustomer\Model;

use APCS\BaseLogger\Logger\Logger;
use APCS\ConsoleCustomer\Api\ConsoleCustomerInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Area;

class CustomerCreator
{
    /**
     * @var State
     */
    private State $state;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CustomerInterfaceFactory
     */
    private CustomerInterfaceFactory $customerInterfaceFactory;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var CustomerFactory
     */
    private CustomerFactory $customerFactory;

    /**
     * @var Logger
     */
    private Logger $apcsLogger;

    /**
     * @param State $state
     * @param StoreManagerInterface $storeManager
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param EncryptorInterface $encryptor
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     * @param Logger $apcsLogger
     */
    public function __construct(
        State $state,
        StoreManagerInterface $storeManager,
        CustomerInterfaceFactory $customerInterfaceFactory,
        EncryptorInterface $encryptor,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        Logger $apcsLogger
    ) {
        $this->state = $state;
        $this->storeManager = $storeManager;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->encryptor = $encryptor;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->apcsLogger = $apcsLogger;
    }

    /**
     * Creating a customer
     *
     * @param array $customerData
     * @return Customer
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(array $customerData): Customer
    {
        if ($this->getCustomer($customerData[ConsoleCustomerInterface::EMAIL])->getData()) {
            throw new LocalizedException(__('Customer with the same email already exist'));
        }

        $this->state->setAreaCode(Area::AREA_FRONTEND);

        if (isset($customerData[ConsoleCustomerInterface::WEBSITE])) {
            $this->createCustomer($customerData, $customerData[ConsoleCustomerInterface::WEBSITE]);
        } else {
            $this->createCustomer($customerData);
        }

        return $this->getCustomer($customerData[ConsoleCustomerInterface::EMAIL]);
    }

    /**
     * Create Customer account
     *
     * @param array $customerData
     * @param int|null $websiteId
     * @return void
     * @throws LocalizedException
     * @throws InputException
     * @throws InputMismatchException
     */
    public function createCustomer(array $customerData, int $websiteId = null): void
    {
        if ($websiteId === null) {
            $websiteId = $this->getWebsiteId();
        }

        $customer = $this->customerInterfaceFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($customerData[ConsoleCustomerInterface::EMAIL]);
        $customer->setFirstname($customerData[ConsoleCustomerInterface::FIRSTNAME]);
        $customer->setLastname($customerData[ConsoleCustomerInterface::LASTNAME]);

        $hashedPassword = $this->encryptor->getHash($customerData[ConsoleCustomerInterface::PASSWORD], true);

        $this->customerRepository->save($customer, $hashedPassword);
    }

    /**
     * Get Customer
     *
     * @param string $email
     * @return Customer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomer(string $email): Customer
    {
        $customer = $this->customerFactory->create();
        return $customer->setWebsiteId($this->getWebsiteId())->loadByEmail($email);
    }

    /**
     * Get Website id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getWebsiteId(): int
    {
        $storeId = $this->storeManager->getStore()->getId();
        return (int)$this->storeManager->getStore($storeId)->getWebsiteId();
    }
}
