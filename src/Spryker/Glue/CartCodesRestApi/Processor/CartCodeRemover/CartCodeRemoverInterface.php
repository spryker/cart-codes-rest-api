<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\CartCodeRemover;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartCodeRemoverInterface
{
    public function removeCartCodeFromCart(
        RestRequestInterface $restRequest
    ): RestResponseInterface;

    public function removeCartCodeFromGuestCart(RestRequestInterface $restRequest): RestResponseInterface;

    public function removeDiscountCodeFromCart(
        RestRequestInterface $restRequest
    ): RestResponseInterface;

    public function removeDiscountCodeFromGuestCart(RestRequestInterface $restRequest): RestResponseInterface;
}
