<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\CartCodeRemover;

use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\CartCodesRestApi\CartCodesRestApiClientInterface;
use Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig;
use Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder\CartCodeRestResponseBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartCodeRemover implements CartCodeRemoverInterface
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

    public function removeDiscountCodeFromCart(RestRequestInterface $restRequest): RestResponseInterface
    {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_CARTS);

        $cartCodeRequestTransfer = (new CartCodeRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setCartCode($restRequest->getResource()->getId());
        $cartCodeOperationResultTransfer = $this->cartCodesRestApiClient->removeCartCode($cartCodeRequestTransfer);

        return $this->cartCodeResponseBuilder->createCartRestResponse($cartCodeOperationResultTransfer, $restRequest);
    }

    public function removeDiscountCodeFromGuestCart(RestRequestInterface $restRequest): RestResponseInterface
    {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_GUEST_CARTS);

        $cartCodeOperationResultTransfer = $this->cartCodesRestApiClient->removeCartCode(
            (new CartCodeRequestTransfer())
                ->setQuote($quoteTransfer)
                ->setCartCode($restRequest->getResource()->getId()),
        );

        return $this->cartCodeResponseBuilder->createGuestCartRestResponse($cartCodeOperationResultTransfer, $restRequest);
    }

    public function removeCartCodeFromCart(RestRequestInterface $restRequest): RestResponseInterface
    {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_CARTS);

        $cartCodeRequestTransfer = (new CartCodeRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setCartCode($restRequest->getResource()->getId());
        $cartCodeOperationResultTransfer = $this->cartCodesRestApiClient->removeCartCodeFromQuote($cartCodeRequestTransfer);

        return $this->cartCodeResponseBuilder->createCartRestResponse($cartCodeOperationResultTransfer, $restRequest);
    }

    public function removeCartCodeFromGuestCart(RestRequestInterface $restRequest): RestResponseInterface
    {
        $quoteTransfer = $this->createQuoteTransfer($restRequest, CartCodesRestApiConfig::RESOURCE_GUEST_CARTS);

        $cartCodeOperationResultTransfer = $this->cartCodesRestApiClient->removeCartCodeFromQuote(
            (new CartCodeRequestTransfer())
                ->setQuote($quoteTransfer)
                ->setCartCode($restRequest->getResource()->getId()),
        );

        return $this->cartCodeResponseBuilder->createGuestCartRestResponse($cartCodeOperationResultTransfer, $restRequest);
    }

    protected function createQuoteTransfer(RestRequestInterface $restRequest, string $resourceType): QuoteTransfer
    {
        $parentResource = $restRequest->findParentResourceByType($resourceType);
        $customerReference = $restRequest->getRestUser()->getNaturalIdentifier();
        $customerTransfer = (new CustomerTransfer())->setCustomerReference($customerReference);

        return (new QuoteTransfer())
            ->setUuid($parentResource->getId())
            ->setCustomer($customerTransfer)
            ->setCustomerReference($customerReference);
    }
}
