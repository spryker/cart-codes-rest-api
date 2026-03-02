<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CartCodesRestApi;

use Spryker\Client\CartCodesRestApi\Dependency\Client\CartCodesRestApiToZedRequestClientInterface;
use Spryker\Client\CartCodesRestApi\Zed\CartCodesRestApiStub;
use Spryker\Client\CartCodesRestApi\Zed\CartCodesRestApiStubInterface;
use Spryker\Client\Kernel\AbstractFactory;

class CartCodesRestApiFactory extends AbstractFactory
{
    public function createCartCodesRestApiZedStub(): CartCodesRestApiStubInterface
    {
        return new CartCodesRestApiStub($this->getZedRequestClient());
    }

    public function getZedRequestClient(): CartCodesRestApiToZedRequestClientInterface
    {
        return $this->getProvidedDependency(CartCodesRestApiDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
