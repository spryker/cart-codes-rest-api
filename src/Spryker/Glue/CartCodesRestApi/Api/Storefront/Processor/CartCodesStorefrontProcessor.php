<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\CartCodesStorefrontResource;
use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestCartCodeRequestAttributesTransfer;

class CartCodesStorefrontProcessor extends AbstractCartCodesStorefrontProcessor
{
    protected const string KEY_CART_ID = 'cartId';

    protected function getCartIdUriVariableName(): string
    {
        return static::KEY_CART_ID;
    }

    protected function resolveCustomerReference(): string
    {
        return $this->getCustomerReference();
    }

    protected function buildQuoteCustomer(string $customerReference): CustomerTransfer
    {
        return $this->getCustomer();
    }

    protected function extractCodeFromTypedData(mixed $data): ?string
    {
        if ($data instanceof RestCartCodeRequestAttributesTransfer) {
            return $data->getCode();
        }

        if ($data instanceof CartCodesStorefrontResource) {
            return $data->code;
        }

        return null;
    }

    protected function executeRemoveCartCode(
        CartCodeRequestTransfer $cartCodeRequestTransfer,
    ): CartCodeResponseTransfer {
        return $this->cartCodesRestApiClient->removeCartCodeFromQuote($cartCodeRequestTransfer);
    }

    protected function getParentResourceClass(): string
    {
        return CartsStorefrontResource::class;
    }
}
