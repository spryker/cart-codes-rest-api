<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartCodesRestApi\Business\CartCodeRemover;

use ArrayObject;
use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Spryker\Shared\CartCodesRestApi\CartCodesRestApiConfig;
use Spryker\Zed\CartCodesRestApi\Dependency\Facade\CartCodesRestApiToCartCodeFacadeInterface;
use Spryker\Zed\CartCodesRestApi\Dependency\Facade\CartCodesRestApiToCartsRestApiFacadeInterface;

class CartCodeRemover implements CartCodeRemoverInterface
{
    /**
     * @var \Spryker\Zed\CartCodesRestApi\Dependency\Facade\CartCodesRestApiToCartCodeFacadeInterface
     */
    protected $cartCodeFacade;

    /**
     * @var \Spryker\Zed\CartCodesRestApi\Dependency\Facade\CartCodesRestApiToCartsRestApiFacadeInterface
     */
    protected $cartsRestApiFacade;

    public function __construct(
        CartCodesRestApiToCartCodeFacadeInterface $cartCodeFacade,
        CartCodesRestApiToCartsRestApiFacadeInterface $cartsRestApiFacade
    ) {
        $this->cartCodeFacade = $cartCodeFacade;
        $this->cartsRestApiFacade = $cartsRestApiFacade;
    }

    public function removeCartCode(CartCodeRequestTransfer $cartCodeRequestTransfer): CartCodeResponseTransfer
    {
        $quoteTransfer = $cartCodeRequestTransfer->getQuote();
        $quoteResponseTransfer = $this->cartsRestApiFacade->findQuoteByUuid($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->createCartCodeOperationResultTransferWithErrorMessageTransfer(
                CartCodesRestApiConfig::ERROR_IDENTIFIER_CART_NOT_FOUND,
            );
        }

        $discountTransfers = $quoteResponseTransfer->getQuoteTransfer()->getVoucherDiscounts();
        if (!$this->isVoucherCodeInQuote($discountTransfers, $cartCodeRequestTransfer->getCartCode())) {
            return $this->createCartCodeOperationResultTransferWithErrorMessageTransfer(
                CartCodesRestApiConfig::ERROR_IDENTIFIER_CART_CODE_NOT_FOUND,
            );
        }

        $cartCodeRequestTransfer->setQuote($quoteResponseTransfer->getQuoteTransfer());

        return $this->cartCodeFacade->removeCartCode($cartCodeRequestTransfer);
    }

    public function removeCartCodeFromQuote(CartCodeRequestTransfer $cartCodeRequestTransfer): CartCodeResponseTransfer
    {
        $quoteTransfer = $cartCodeRequestTransfer->getQuote();
        $quoteResponseTransfer = $this->cartsRestApiFacade->findQuoteByUuid($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->createCartCodeOperationResultTransferWithErrorMessageTransfer(
                CartCodesRestApiConfig::ERROR_IDENTIFIER_CART_NOT_FOUND,
            );
        }

        $cartCodeRequestTransfer->setQuote($quoteResponseTransfer->getQuoteTransfer());

        $cartCodeResponseTransfer = $this->cartCodeFacade->removeCartCode($cartCodeRequestTransfer);

        if (!$cartCodeResponseTransfer->getIsSuccessful()) {
            return $this->createCartCodeOperationResultTransferWithErrorMessageTransfer(
                CartCodesRestApiConfig::ERROR_IDENTIFIER_CART_CODE_CANNOT_BE_REMOVED,
            );
        }

        return $cartCodeResponseTransfer;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\DiscountTransfer> $discountTransfers
     * @param string $voucherCode
     *
     * @return bool
     */
    protected function isVoucherCodeInQuote(ArrayObject $discountTransfers, string $voucherCode): bool
    {
        foreach ($discountTransfers as $discountTransfer) {
            if ($discountTransfer->getVoucherCode() === $voucherCode) {
                return true;
            }
        }

        return false;
    }

    protected function createCartCodeOperationResultTransferWithErrorMessageTransfer(string $errorIdentifier): CartCodeResponseTransfer
    {
        return (new CartCodeResponseTransfer())->addMessage(
            (new MessageTransfer())->setValue($errorIdentifier),
        );
    }
}
