<?php

declare(strict_types=1);

namespace APCS\ConsoleCustomer\Service;

use Magento\Framework\Exception\LocalizedException;
use APCS\ConsoleCustomer\Api\ConsoleCustomerInterface;

class Validator
{
    /**
     * Check console input
     *
     * @param array $input
     * @return bool
     * @throws LocalizedException
     */
    public function validate(array $input): bool
    {
        if (!isset($input[ConsoleCustomerInterface::FIRSTNAME])) {
            throw new LocalizedException(__('Fist Name is required attribute'));
        }
        if (!isset($input[ConsoleCustomerInterface::LASTNAME])) {
            throw new LocalizedException(__('Last Name is required attribute'));
        }
        if (!isset($input[ConsoleCustomerInterface::EMAIL])) {
            throw new LocalizedException(__('Email is required attribute'));
        }
        if (!strpos($input[ConsoleCustomerInterface::EMAIL], '@')) {
            throw new LocalizedException(__('Email is not a valid email address.'));
        }
        if (!isset($input[ConsoleCustomerInterface::PASSWORD])) {
            throw new LocalizedException(__('Password is required attribute'));
        }
        return true;
    }
}
