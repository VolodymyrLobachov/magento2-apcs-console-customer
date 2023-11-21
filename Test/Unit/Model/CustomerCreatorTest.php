<?php

declare(strict_types=1);

namespace APCS\ConsoleCustomer\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use APCS\ConsoleCustomer\Model\CustomerCreator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CustomerCreatorTest extends TestCase
{
    /**
     * @var CustomerCreator
     */
    private $customerCreator;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $stateMock = $this->getMockBuilder(\Magento\Framework\App\State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stateMock->method('getAreaCode')
            ->willReturn(\Magento\Framework\App\Area::AREA_FRONTEND);

        $encryptorMock = $this->getMockBuilder(\Magento\Framework\Encryption\EncryptorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerRepositoryMock = $this->getMockBuilder(\Magento\Customer\Api\CustomerRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $apcsLoggerMock = $this->getMockBuilder(\APCS\BaseLogger\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeMock = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)
            ->getMock();

        $storeManagerMock->method('getStore')
            ->willReturn($storeMock);

        $storeMock->method('getId')
            ->willReturn(1);

        $customerMock = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'loadByEmail'])
            ->addMethods(['setWebsiteId'])
            ->getMock();

        $customerFactoryMock = $this->getMockBuilder(\Magento\Customer\Model\CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerFactoryMock->method('create')
            ->willReturn($customerMock);

        $customerMock->method('setWebsiteId')
            ->with(0)
            ->willReturn($customerMock);

        $customerMock->method('loadByEmail')
            ->willReturn($customerMock);

        $customerMock->method('getData')
            ->willReturn(null);

        $customerInterfaceFactoryMock = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerInterfaceMock = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterface::class)
            ->getMock();

        $customerInterfaceFactoryMock->method('create')
            ->willReturn($customerInterfaceMock);

        $this->customerCreator = $objectManager->getObject(
            CustomerCreator::class,
            [
                'state' => $stateMock,
                'storeManager' => $storeManagerMock,
                'customerInterfaceFactory' => $customerInterfaceFactoryMock,
                'encryptor' => $encryptorMock,
                'customerRepository' => $customerRepositoryMock,
                'customerFactory' => $customerFactoryMock,
                'apcsLogger' => $apcsLoggerMock,
            ]
        );
    }

    /**
     * Test customer creation
     */
    public function testCustomerCreation()
    {
        $customerData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'password' => 'password123',
        ];

        $createdCustomer = $this->customerCreator->execute($customerData);

        $this->assertInstanceOf(\Magento\Customer\Model\Customer::class, $createdCustomer);
    }
}
