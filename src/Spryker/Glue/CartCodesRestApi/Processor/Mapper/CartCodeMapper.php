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
    protected $config;

    /**
     * @param \Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig $config
     */
    public function __construct(CartCodesRestApiConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $messageTransfer
     * @param \Generated\Shared\Transfer\RestErrorMessageTransfer $restErrorMessageTransfer
     *
     * @return \Generated\Shared\Transfer\RestErrorMessageTransfer
     */
    public function mapMessageTransferToRestErrorMessageTransfer(
        MessageTransfer $messageTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer
    ): RestErrorMessageTransfer {
        $errorIdentifier = $messageTransfer->getValue();
        $errorIdentifierToRestErrorMapping = $this->config->getErrorIdentifierToRestErrorMapping();
        if ($errorIdentifier && isset($errorIdentifierToRestErrorMapping[$errorIdentifier])) {
            $errorIdentifierMapping = $errorIdentifierToRestErrorMapping[$errorIdentifier];
            $restErrorMessageTransfer->fromArray($errorIdentifierMapping, true);

            return $restErrorMessageTransfer;
        }

        return $restErrorMessageTransfer;
    }
}
