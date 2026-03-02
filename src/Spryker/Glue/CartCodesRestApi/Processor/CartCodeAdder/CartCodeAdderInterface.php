<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\CartCodeAdder;

use Generated\Shared\Transfer\RestCartCodeRequestAttributesTransfer;
use Generated\Shared\Transfer\RestDiscountsRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartCodeAdderInterface
{
    public function addDiscountCodeToCart(
        RestRequestInterface $restRequest,
        RestDiscountsRequestAttributesTransfer $restDiscountRequestAttributesTransfer
    ): RestResponseInterface;

    public function addDiscountCodeToGuestCart(
        RestRequestInterface $restRequest,
        RestDiscountsRequestAttributesTransfer $restDiscountRequestAttributesTransfer
    ): RestResponseInterface;

    public function addCartCodeToCart(
        RestRequestInterface $restRequest,
        RestCartCodeRequestAttributesTransfer $restCartCodeRequestAttributesTransfer
    ): RestResponseInterface;

    public function addCartCodeToGuestCart(
        RestRequestInterface $restRequest,
        RestCartCodeRequestAttributesTransfer $restCartCodeRequestAttributesTransfer
    ): RestResponseInterface;
}
