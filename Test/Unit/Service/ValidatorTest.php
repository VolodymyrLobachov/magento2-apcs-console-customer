<?php

declare(strict_types=1);

namespace APCS\ConsoleCustomer\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use APCS\ConsoleCustomer\Service\Validator;
use Magento\Framework\Exception\LocalizedException;
use APCS\ConsoleCustomer\Api\ConsoleCustomerInterface;

/**
 * @covers \APCS\ConsoleCustomer\Service\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private ObjectManager $objectManager;

    /**
     * @var Validator
     */
    private $validatorService;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->validatorService = $this->objectManager->getObject(Validator::class);
    }

    /**
     * Test validation with valid input
     *
     * @throws LocalizedException
     */
    public function testValidateSuccess() {
        $input = [
            ConsoleCustomerInterface::FIRSTNAME => 'John',
            ConsoleCustomerInterface::LASTNAME => 'Doe',
            ConsoleCustomerInterface::EMAIL => 'john.doe@example.com',
            ConsoleCustomerInterface::PASSWORD => 'password123',
        ];

        $this->assertTrue($this->validatorService->validate($input));
    }

    public function testValidationWithMissingAttribute(): void
    {
        $input = [
            ConsoleCustomerInterface::LASTNAME => 'Doe',
            ConsoleCustomerInterface::EMAIL => 'john.doe@example.com',
            ConsoleCustomerInterface::PASSWORD => 'password123',
        ];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Fist Name is required attribute');

        $this->validatorService->validate($input);
    }

    /**
     * Test validation with invalid email address
     */
    public function testValidationWithInvalidEmail(): void
    {
        $input = [
            ConsoleCustomerInterface::FIRSTNAME => 'John',
            ConsoleCustomerInterface::LASTNAME => 'Doe',
            ConsoleCustomerInterface::EMAIL => 'invalid_email',
            ConsoleCustomerInterface::PASSWORD => 'password123',
        ];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Email is not a valid email address.');

        $this->validatorService->validate($input);
    }
}
