<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder;

use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartCodeRestResponseBuilderInterface
{
    public function createCartRestResponse(
        CartCodeResponseTransfer $cartCodeResponseTransfer,
        RestRequestInterface $restRequest
    ): RestResponseInterface;

    public function createGuestCartRestResponse(
        CartCodeResponseTransfer $cartCodeResponseTransfer,
        RestRequestInterface $restRequest
    ): RestResponseInterface;
}
