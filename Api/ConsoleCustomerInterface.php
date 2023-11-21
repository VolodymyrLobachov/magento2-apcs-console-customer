<?php

declare(strict_types=1);

namespace APCS\ConsoleCustomer\Api;

interface ConsoleCustomerInterface
{
    /**
     *  Success code
     */
    public const SUCCESS_CODE = 0;

    /**
     * Failure code
     */
    public const FAILURE_CODE = 1;

    /**
     * First name for customer
     */
    public const FIRSTNAME = 'firstname';

    /**
     * Last name for customer
     */
    public const LASTNAME = 'lastname';

    /**
     * Email for customer
     */
    public const EMAIL = 'email';

    /**
     * Password customer
     */
    public const PASSWORD = 'password';

    /**
     * Website for customer
     */
    public const WEBSITE = 'website-id';

    /**
     * Flag to show all customer data for customer
     */
    public const SHOW_ALL_CUSTOMER_DATA = 'all-data';
}
