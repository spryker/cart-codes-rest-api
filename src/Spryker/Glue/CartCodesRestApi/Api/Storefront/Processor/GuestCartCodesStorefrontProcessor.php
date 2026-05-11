<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\GuestCartCodesStorefrontResource;
use Generated\Api\Storefront\GuestCartsStorefrontResource;
use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\RestCartCodeRequestAttributesTransfer;

class GuestCartCodesStorefrontProcessor extends AbstractGuestCartCodesStorefrontProcessor
{
    protected function extractCodeFromTypedData(mixed $data): ?string
    {
        if ($data instanceof RestCartCodeRequestAttributesTransfer) {
            return $data->getCode();
        }

        if ($data instanceof GuestCartCodesStorefrontResource) {
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
        return GuestCartsStorefrontResource::class;
    }
}
