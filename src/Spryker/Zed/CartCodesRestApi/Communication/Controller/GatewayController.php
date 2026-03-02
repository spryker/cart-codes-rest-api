<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartCodesRestApi\Communication\Controller;

use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\CartCodesRestApi\Business\CartCodesRestApiFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    public function addCartCodeAction(CartCodeRequestTransfer $cartCodeRequestTransfer): CartCodeResponseTransfer
    {
        return $this->getFacade()->addCartCode($cartCodeRequestTransfer);
    }

    public function removeCartCodeAction(CartCodeRequestTransfer $cartCodeRequestTransfer): CartCodeResponseTransfer
    {
        return $this->getFacade()->removeCartCode($cartCodeRequestTransfer);
    }

    public function removeCartCodeFromQuoteAction(CartCodeRequestTransfer $cartCodeRequestTransfer): CartCodeResponseTransfer
    {
        return $this->getFacade()->removeCartCodeFromQuote($cartCodeRequestTransfer);
    }
}
