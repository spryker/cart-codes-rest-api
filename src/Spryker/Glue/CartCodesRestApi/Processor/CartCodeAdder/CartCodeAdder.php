<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\CartCodeAdder;

use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartCodeRequestAttributesTransfer;
use Generated\Shared\Transfer\RestDiscountsRequestAttributesTransfer;
use Spryker\Client\CartCodesRestApi\CartCodesRestApiClientInterface;
use Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig;
use Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder\CartCodeRestResponseBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartCodeAdder implements CartCodeAdderInterface
{
    /**
     * @var \Spryker\Client\CartCodesRestApi\CartCodesRestApiClientInterface
     */
    protected $cartCodesRestApiClient;

    /**
     * @var \Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder\CartCodeRestResponseBuilderInterface
     */
    protected $cartCodeResponseBuilder;

    public function __construct(
        CartCodesRestApiClientInterface $cartCodesClient,
        CartCodeRestResponseBuilderInterface $cartCodeResponseBuilder
    ) {
        $this->cartCodesRestApiClient = $cartCodesClient;
        $this->cartCodeResponseBuilder = $cartCodeResponseBuilder;
    }

    public function addDiscountCodeToCart(
        RestRequestInterface $restRequest,
        RestDiscountsRequestAttributesTransfer $restDiscountRequestAttributesTransfer
    ): RestResponseInterface {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_CARTS);
        $cartCodeResponseTransfer = $this->addCartCode($restDiscountRequestAttributesTransfer->getCode(), $quoteTransfer);

        return $this->cartCodeResponseBuilder->createCartRestResponse($cartCodeResponseTransfer, $restRequest);
    }

    public function addDiscountCodeToGuestCart(
        RestRequestInterface $restRequest,
        RestDiscountsRequestAttributesTransfer $restDiscountRequestAttributesTransfer
    ): RestResponseInterface {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_GUEST_CARTS);
        $cartCodeResponseTransfer = $this->addCartCode($restDiscountRequestAttributesTransfer->getCode(), $quoteTransfer);

        return $this->cartCodeResponseBuilder->createGuestCartRestResponse($cartCodeResponseTransfer, $restRequest);
    }

    public function addCartCodeToCart(
        RestRequestInterface $restRequest,
        RestCartCodeRequestAttributesTransfer $restCartCodeRequestAttributesTransfer
    ): RestResponseInterface {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_CARTS);
        $cartCodeResponseTransfer = $this->addCartCode($restCartCodeRequestAttributesTransfer->getCode(), $quoteTransfer);

        return $this->cartCodeResponseBuilder->createCartRestResponse($cartCodeResponseTransfer, $restRequest);
    }

    public function addCartCodeToGuestCart(
        RestRequestInterface $restRequest,
        RestCartCodeRequestAttributesTransfer $restCartCodeRequestAttributesTransfer
    ): RestResponseInterface {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_GUEST_CARTS);
        $cartCodeResponseTransfer = $this->addCartCode($restCartCodeRequestAttributesTransfer->getCode(), $quoteTransfer);

        return $this->cartCodeResponseBuilder->createGuestCartRestResponse($cartCodeResponseTransfer, $restRequest);
    }

    protected function addCartCode(
        string $cartCode,
        QuoteTransfer $quoteTransfer
    ): CartCodeResponseTransfer {
        $cartCodeRequestTransfer = (new CartCodeRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setCartCode($cartCode);

        return $this->cartCodesRestApiClient->addCartCode($cartCodeRequestTransfer);
    }

    protected function createQuoteTransfer(RestRequestInterface $restRequest, string $resourceType): QuoteTransfer
    {
        $cartResource = $restRequest->findParentResourceByType($resourceType);
        $customerReference = $restRequest->getRestUser()->getNaturalIdentifier();
        $idCustomer = $restRequest->getRestUser()->getSurrogateIdentifier();
        $customerTransfer = (new CustomerTransfer())
            ->setCustomerReference($customerReference)
            ->setIdCustomer($idCustomer);

        return (new QuoteTransfer())
            ->setUuid($cartResource ? $cartResource->getId() : null)
            ->setCustomer($customerTransfer)
            ->setCustomerReference($customerReference);
    }
}
