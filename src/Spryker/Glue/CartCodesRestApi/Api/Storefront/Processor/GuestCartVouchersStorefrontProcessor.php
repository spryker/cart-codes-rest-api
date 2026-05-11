<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\GuestCartsStorefrontResource;
use Generated\Api\Storefront\GuestCartVouchersStorefrontResource;
use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\RestDiscountsRequestAttributesTransfer;

class GuestCartVouchersStorefrontProcessor extends AbstractGuestCartCodesStorefrontProcessor
{
    protected function extractCodeFromTypedData(mixed $data): ?string
    {
        if ($data instanceof RestDiscountsRequestAttributesTransfer) {
            return $data->getCode();
        }

        if ($data instanceof GuestCartVouchersStorefrontResource) {
            return $data->code;
        }

        return null;
    }

    protected function executeRemoveCartCode(
        CartCodeRequestTransfer $cartCodeRequestTransfer,
    ): CartCodeResponseTransfer {
        return $this->cartCodesRestApiClient->removeCartCode($cartCodeRequestTransfer);
    }

    protected function getParentResourceClass(): string
    {
        return GuestCartsStorefrontResource::class;
    }
}
