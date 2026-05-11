<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Exception;

use Generated\Shared\Transfer\CartCodeResponseTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds pre-configured `GlueApiException` instances for known cart-code error scenarios.
 *
 * Uses {@see CartCodesRestApiConfig::getErrorIdentifierToRestErrorMapping()} as the source of
 * truth for `errorIdentifier → [code, status, detail]` translation, keeping JSON:API responses
 * byte-equivalent to the legacy stack.
 */
class CartCodesExceptionFactory
{
    /**
     * @uses \Spryker\Glue\CartsRestApi\CartsRestApiConfig::RESPONSE_CODE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY
     */
    protected const string RESPONSE_CODE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY = '109';

    /**
     * @uses \Spryker\Glue\CartsRestApi\CartsRestApiConfig::EXCEPTION_MESSAGE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY
     */
    protected const string EXCEPTION_MESSAGE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY = 'Anonymous customer unique id is empty.';

    public function __construct(
        protected CartCodesRestApiConfig $cartCodesRestApiConfig,
    ) {
    }

    public function createCartIdMissingException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            CartCodesRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND,
            CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND,
        );
    }

    public function createAnonymousCustomerUniqueIdEmptyException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            static::RESPONSE_CODE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY,
            static::EXCEPTION_MESSAGE_ANONYMOUS_CUSTOMER_UNIQUE_ID_EMPTY,
        );
    }

    public function createCartNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            CartCodesRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND,
            CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND,
        );
    }

    public function createCartCodeNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            CartCodesRestApiConfig::RESPONSE_CODE_CART_CODE_NOT_FOUND,
            CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_CODE_NOT_FOUND,
        );
    }

    public function createCartCodeCantBeAddedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            CartCodesRestApiConfig::RESPONSE_CODE_CART_CODE_CANT_BE_ADDED,
            CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_CODE_CANT_BE_ADDED,
        );
    }

    public function createCartCodeCannotBeRemovedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            CartCodesRestApiConfig::RESPONSE_CODE_CART_CODE_CANNOT_BE_REMOVED,
            CartCodesRestApiConfig::EXCEPTION_MESSAGE_CART_CODE_CANNOT_BE_REMOVED,
        );
    }

    /**
     * Builds a `GlueApiException` from the first error in a non-successful
     * `CartCodeResponseTransfer`. Looks up `errorIdentifier` in
     * {@see CartCodesRestApiConfig::getErrorIdentifierToRestErrorMapping()} and falls back to
     * the supplied default code/detail/status when no mapping matches.
     */
    public function createExceptionFromCartCodeResponse(
        CartCodeResponseTransfer $cartCodeResponseTransfer,
        string $fallbackCode,
        string $fallbackDetail,
        int $fallbackStatus = Response::HTTP_UNPROCESSABLE_ENTITY,
    ): GlueApiException {
        $messages = $cartCodeResponseTransfer->getMessages();

        if ($messages->count() === 0) {
            return new GlueApiException($fallbackStatus, $fallbackCode, $fallbackDetail);
        }

        /** @var \Generated\Shared\Transfer\MessageTransfer $firstMessage */
        $firstMessage = $messages->offsetGet(0);
        $mapped = $this->mapCartCodeMessage($firstMessage);

        if ($mapped !== null) {
            return $mapped;
        }

        $detail = $firstMessage->getValue() ?? $fallbackDetail;

        return new GlueApiException($fallbackStatus, $fallbackCode, $detail);
    }

    public function mapCartCodeMessage(MessageTransfer $messageTransfer): ?GlueApiException
    {
        $errorIdentifier = $messageTransfer->getValue();

        if ($errorIdentifier === null) {
            return null;
        }

        $mapping = $this->cartCodesRestApiConfig->getErrorIdentifierToRestErrorMapping();

        if (!isset($mapping[$errorIdentifier])) {
            return null;
        }

        $entry = $mapping[$errorIdentifier];

        return new GlueApiException(
            (int)$entry[RestErrorMessageTransfer::STATUS],
            (string)$entry[RestErrorMessageTransfer::CODE],
            (string)$entry[RestErrorMessageTransfer::DETAIL],
        );
    }
}
