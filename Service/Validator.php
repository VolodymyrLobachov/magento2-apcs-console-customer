<?php
declare(strict_types=1);

namespace APCS\ConsoleCustomer\Service;

use Magento\Framework\Exception\LocalizedException;

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
        if (!isset($input['firstname'])) {
            throw new LocalizedException(__('Fist Name is required attribute'));
        }
        if (!isset($input['lastname'])) {
            throw new LocalizedException(__('Last Name is required attribute'));
        }
        if (!isset($input['email'])) {
            throw new LocalizedException(__('Email is required attribute'));
        }
        if (!strpos($input['email'], '@')) {
            throw new LocalizedException(__('Email is not a valid email address.'));
        }
        if (!isset($input['password'])) {
            throw new LocalizedException(__('Password is required attribute'));
        }
        return true;
    }
}
