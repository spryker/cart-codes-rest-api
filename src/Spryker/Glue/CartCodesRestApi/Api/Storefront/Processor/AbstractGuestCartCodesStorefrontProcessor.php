<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Processor;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;

/**
 * Adds the anonymous customer wiring shared by `GuestCartCodes` and `GuestCartVouchers`
 * processors: cart UUID is read from `guestCartId`, the customer reference is derived
 * from the `X-Anonymous-Customer-Unique-Id` header, and the `QuoteTransfer.customer`
 * payload carries only that anonymous reference (no bearer customer is available).
 */
abstract class AbstractGuestCartCodesStorefrontProcessor extends AbstractCartCodesStorefrontProcessor
{
    protected const string KEY_CART_ID = 'guestCartId';

    /**
     * @uses \Spryker\Shared\PersistentCart\PersistentCartConfig::PERSISTENT_CART_ANONYMOUS_PREFIX
     */
    protected const string ANONYMOUS_CUSTOMER_REFERENCE_PREFIX = 'anonymous:';

    protected function getCartIdUriVariableName(): string
    {
        return static::KEY_CART_ID;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveCustomerReference(): string
    {
        $anonymousCustomerUniqueId = $this->getRequest()->headers->get(
            CartsRestApiConfig::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID,
        );

        if ($anonymousCustomerUniqueId === null || $anonymousCustomerUniqueId === '') {
            throw $this->exceptionFactory->createAnonymousCustomerUniqueIdEmptyException();
        }

        return static::ANONYMOUS_CUSTOMER_REFERENCE_PREFIX . $anonymousCustomerUniqueId;
    }

    protected function buildQuoteCustomer(string $customerReference): CustomerTransfer
    {
        return (new CustomerTransfer())->setCustomerReference($customerReference);
    }
}
