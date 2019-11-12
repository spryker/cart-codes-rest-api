<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi;

use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\Kernel\AbstractBundleConfig;
use Spryker\Shared\CartCodesRestApi\CartCodesRestApiConfig as CartCodesRestApiSharedConfig;
use Symfony\Component\HttpFoundation\Response;

class CartCodesRestApiConfig extends AbstractBundleConfig
{
    public const RESOURCE_DISCOUNTS = 'discounts';
    public const CONTROLLER_CART_DISCOUNTS = 'cart-discounts-resource';
    public const CONTROLLER_GUEST_CART_DISCOUNTS = 'guest-cart-discounts-resource';

    /**
     * @uses \Spryker\Glue\CartsRestApi\CartsRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND
     */
    public const RESPONSE_CODE_CART_NOT_FOUND = '101';
    public const RESPONSE_CART_CODE_CANT_BE_DELETED = '3302';

    /**
     * @uses \Spryker\Glue\CartsRestApi\CartsRestApiConfig::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND
     */
    public const EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND = 'Cart with given uuid not found.';
    public const EXCEPTION_MESSAGE_CART_CODE_CANT_BE_DELETED = 'Cart code can\'t be deleted.';

    /**
     * @return array
     */
    public function getErrorIdentifierToRestErrorMapping(): array
    {
        return [
            CartCodesRestApiSharedConfig::ERROR_IDENTIFIER_CART_NOT_FOUND => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_NOT_FOUND,
                RestErrorMessageTransfer::STATUS => Response::HTTP_NOT_FOUND,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND,
            ],
            CartCodesRestApiSharedConfig::ERROR_IDENTIFIER_CART_CODE_CANT_BE_DELETED => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CART_CODE_CANT_BE_DELETED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::EXCEPTION_MESSAGE_CART_CODE_CANT_BE_DELETED,
            ],
        ];
    }
}
