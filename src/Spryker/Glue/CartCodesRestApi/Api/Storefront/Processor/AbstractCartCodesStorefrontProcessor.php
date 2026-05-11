<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Api\Storefront\GuestCartsStorefrontResource;
use Generated\Shared\Transfer\CartCodeRequestTransfer;
use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\CartCodesRestApi\CartCodesRestApiClientInterface;
use Spryker\Glue\CartCodesRestApi\Api\Storefront\Exception\CartCodesExceptionFactory;
use Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig;
use Spryker\Glue\CartsRestApi\Api\Storefront\Mapper\StorefrontCartMapperInterface;
use Spryker\Service\Serializer\SerializerServiceInterface;

/**
 * Shared scaffold for the four cart-code/voucher Storefront processors
 * (`CartCodes`/`CartVouchers` and their `GuestCart*` siblings). Carries the common
 * `addCartCode`/`removeCartCodeFromQuote`/`removeCartCode` orchestration; concrete
 * subclasses only declare what differs:
 *  - the cart URI variable name (`cartId` vs `guestCartId`),
 *  - how the customer reference is resolved (bearer customer vs anonymous header),
 *  - how the `QuoteTransfer.customer` payload is built,
 *  - how the request `code` is read from the typed input ({@see RestCartCodeRequestAttributesTransfer}
 *    or `RestDiscountsRequestAttributesTransfer` plus the matching Storefront resource),
 *  - which client method removes the code (Codes use `removeCartCodeFromQuote`,
 *    Vouchers use `removeCartCode`),
 *  - which parent resource class to emit ({@see \Generated\Api\Storefront\CartsStorefrontResource}
 *    vs {@see \Generated\Api\Storefront\GuestCartsStorefrontResource}).
 */
abstract class AbstractCartCodesStorefrontProcessor extends AbstractStorefrontProcessor
{
    protected const string KEY_CODE = 'code';

    public function __construct(
        protected CartCodesRestApiClientInterface $cartCodesRestApiClient,
        protected StorefrontCartMapperInterface $cartMapper,
        protected SerializerServiceInterface $serializer,
        protected CartCodesExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * URI variable name carrying the cart UUID, e.g. `cartId` for authenticated carts
     * and `guestCartId` for guest sessions.
     */
    abstract protected function getCartIdUriVariableName(): string;

    /**
     * Returns the customer reference for the request: the bearer customer's reference
     * for authenticated processors, or the synthetic `anonymous:<id>` reference for guest
     * processors. Throws when guest header is missing.
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    abstract protected function resolveCustomerReference(): string;

    /**
     * Builds the `CustomerTransfer` placed onto the `QuoteTransfer` issued to the client.
     * Authenticated processors return the resolved bearer customer; guest processors
     * return a new transfer carrying just the anonymous customer reference.
     */
    abstract protected function buildQuoteCustomer(string $customerReference): CustomerTransfer;

    /**
     * Extracts the cart code from a typed POST body (request attributes transfer or the
     * Storefront resource generated from the resource yml). Returns `null` when neither
     * type matches; the array fallback is handled in {@see resolveCodeFromData()}.
     */
    abstract protected function extractCodeFromTypedData(mixed $data): ?string;

    /**
     * Calls the client method that removes the code from the quote — the two CartCodes
     * processors call `removeCartCodeFromQuote`, the two Vouchers processors call
     * `removeCartCode` (legacy method names kept for BC).
     */
    abstract protected function executeRemoveCartCode(
        CartCodeRequestTransfer $cartCodeRequestTransfer,
    ): CartCodeResponseTransfer;

    /**
     * Fully-qualified class name of the parent resource emitted as the response body —
     * `CartsStorefrontResource::class` for auth, `GuestCartsStorefrontResource::class` for guest.
     *
     * @return class-string<\Generated\Api\Storefront\CartsStorefrontResource|\Generated\Api\Storefront\GuestCartsStorefrontResource>
     */
    abstract protected function getParentResourceClass(): string;

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): mixed
    {
        $cartUuid = $this->resolveCartUuid();
        $code = $this->extractCodeFromData($data);
        $customerReference = $this->resolveCustomerReference();

        $cartCodeRequestTransfer = (new CartCodeRequestTransfer())
            ->setQuote($this->buildQuoteTransfer($cartUuid, $customerReference))
            ->setCartCode($code);

        $cartCodeResponseTransfer = $this->cartCodesRestApiClient->addCartCode($cartCodeRequestTransfer);

        if (!$cartCodeResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromCartCodeResponse(
                $cartCodeResponseTransfer,
                CartCodesRestApiConfig::RESPONSE_CODE_CART_CODE_CANT_BE_ADDED,
                CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_CODE_CANT_BE_ADDED,
            );
        }

        return $this->mapQuoteTransferToParentResource($cartCodeResponseTransfer->getQuoteOrFail());
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processDelete(): mixed
    {
        $cartUuid = $this->resolveCartUuid();
        $code = $this->resolveCode();
        $customerReference = $this->resolveCustomerReference();

        $cartCodeRequestTransfer = (new CartCodeRequestTransfer())
            ->setQuote($this->buildQuoteTransfer($cartUuid, $customerReference))
            ->setCartCode($code);

        $cartCodeResponseTransfer = $this->executeRemoveCartCode($cartCodeRequestTransfer);

        if (!$cartCodeResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromCartCodeResponse(
                $cartCodeResponseTransfer,
                CartCodesRestApiConfig::RESPONSE_CODE_CART_CODE_CANNOT_BE_REMOVED,
                CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_CODE_CANNOT_BE_REMOVED,
            );
        }

        return null;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveCartUuid(): string
    {
        $cartUuid = $this->getUriVariables()[$this->getCartIdUriVariableName()] ?? null;

        if (!is_string($cartUuid) || $cartUuid === '') {
            throw $this->exceptionFactory->createCartIdMissingException();
        }

        return $cartUuid;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveCode(): string
    {
        $code = $this->getUriVariables()[static::KEY_CODE] ?? null;

        if (!is_string($code) || $code === '') {
            throw $this->exceptionFactory->createCartCodeNotFoundException();
        }

        return $code;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function extractCodeFromData(mixed $data): string
    {
        $code = $this->resolveCodeFromData($data);

        if (!is_string($code) || $code === '') {
            throw $this->exceptionFactory->createCartCodeCantBeAddedException();
        }

        return $code;
    }

    protected function resolveCodeFromData(mixed $data): mixed
    {
        $code = $this->extractCodeFromTypedData($data);

        if ($code !== null) {
            return $code;
        }

        if (is_array($data)) {
            return $data[static::KEY_CODE] ?? null;
        }

        return null;
    }

    protected function buildQuoteTransfer(string $cartUuid, string $customerReference): QuoteTransfer
    {
        return (new QuoteTransfer())
            ->setUuid($cartUuid)
            ->setCustomerReference($customerReference)
            ->setCustomer($this->buildQuoteCustomer($customerReference));
    }

    /**
     * Denormalizes the result of {@see CartCodesRestApiClientInterface::addCartCode()}
     * into the parent resource (`Carts` or `GuestCarts`) and pre-loads the
     * QuoteTransfer-derived collections that the relationship resolvers later read.
     */
    protected function mapQuoteTransferToParentResource(
        QuoteTransfer $quoteTransfer,
    ): CartsStorefrontResource|GuestCartsStorefrontResource {
        $restCartsAttributesTransfer = $this->cartMapper->mapQuoteTransferToRestCartsAttributesTransfer($quoteTransfer);

        /** @var \Generated\Api\Storefront\CartsStorefrontResource|\Generated\Api\Storefront\GuestCartsStorefrontResource $resource */
        $resource = $this->serializer->denormalize(
            ['uuid' => $quoteTransfer->getUuid()] + $restCartsAttributesTransfer->toArray(true, true),
            $this->getParentResourceClass(),
        );

        $resource->voucherDiscounts = iterator_to_array($quoteTransfer->getVoucherDiscounts());
        $resource->cartRuleDiscounts = iterator_to_array($quoteTransfer->getCartRuleDiscounts());
        $resource->promotionItems = iterator_to_array($quoteTransfer->getPromotionItems());
        $resource->giftCards = iterator_to_array($quoteTransfer->getGiftCards());
        $resource->items = iterator_to_array($quoteTransfer->getItems());

        return $resource;
    }
}
