<?php
declare(strict_types=1);

namespace APCS\ConsoleCustomer\Model;

use Magento\Framework\App\State;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use APCS\BaseLogger\Logger\Logger;

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
     * @return Customer|null
     */
    public function execute(array $customerData): ?Customer
    {
        try {
            if (!$this->getCustomer($customerData['email'])->getData()) {
                $this->state->setAreaCode('frontend');
                if (isset($customerData['website-id'])) {
                    $this->createCustomer($customerData, $customerData['website-id']);
                } else {
                    $this->createCustomer($customerData);
                }
                //check if customer created
                return $this->getCustomer($customerData['email']);
            }
        } catch (LocalizedException $localizedException) {
            $this->apcsLogger->error($localizedException->getMessage());
        }
        return null;
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
        $customer->setEmail($customerData['email']);
        $customer->setFirstname($customerData['firstname']);
        $customer->setLastname($customerData['lastname']);
        $hashedPassword = $this->encryptor->getHash($customerData['password'], true);

        $this->customerRepository->save($customer, $hashedPassword);
    }

    /**
     * Get Customer
     *
     * @param string $email
     * @return Customer|null
     */
    public function getCustomer(string $email): ?Customer
    {
        try {
            $websiteId = $this->getWebsiteId();
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId)->loadByEmail($email);

            return $customer;
        } catch (LocalizedException $localizedException) {
            $this->apcsLogger->error($localizedException->getMessage());
            return null;
        }
    }

    /**
     * Get Website id
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            return (int)$this->storeManager->getStore($storeId)->getWebsiteId();
        } catch (LocalizedException $localizedException) {
            $this->apcsLogger->error($localizedException->getMessage());
            return null;
        }
    }
}
