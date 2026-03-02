<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\Mapper;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig;

class CartCodeMapper implements CartCodeMapperInterface
{
    /**
     * @var \Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig
     */
    protected $cartCodesRestApiConfig;

    public function __construct(CartCodesRestApiConfig $cartCodesRestApiConfig)
    {
        $this->cartCodesRestApiConfig = $cartCodesRestApiConfig;
    }

    public function mapMessageTransferToRestErrorMessageTransfer(
        MessageTransfer $messageTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer
    ): RestErrorMessageTransfer {
        $errorIdentifier = $messageTransfer->getValue();
        $errorIdentifierToRestErrorMapping = $this->cartCodesRestApiConfig->getErrorIdentifierToRestErrorMapping();
        if ($errorIdentifier && isset($errorIdentifierToRestErrorMapping[$errorIdentifier])) {
            $errorIdentifierMapping = $errorIdentifierToRestErrorMapping[$errorIdentifier];
            $restErrorMessageTransfer->fromArray($errorIdentifierMapping, true);

            return $restErrorMessageTransfer;
        }

        return $restErrorMessageTransfer;
    }
}
