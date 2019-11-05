<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CartCodesRestApi;

use Spryker\Client\CartCodesRestApi\CartCodeAdder\CartCodeAdder;
use Spryker\Client\CartCodesRestApi\CartCodeAdder\CartCodeAdderInterface;
use Spryker\Client\CartCodesRestApi\CartCodeDeleter\CartCodeDeleter;
use Spryker\Client\CartCodesRestApi\CartCodeDeleter\CartCodeDeleterInterface;
use Spryker\Client\CartCodesRestApi\Dependency\Client\CartCodesRestApiToZedRequestClientInterface;
use Spryker\Client\CartCodesRestApi\Zed\CartCodesRestApiStub;
use Spryker\Client\CartCodesRestApi\Zed\CartCodesRestApiStubInterface;
use Spryker\Client\Kernel\AbstractFactory;

class CartCodesRestApiFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\CartCodesRestApi\CartCodeAdder\CartCodeAdderInterface
     */
    public function createCartCodeAdder(): CartCodeAdderInterface
    {
        return new CartCodeAdder($this->createCartCodesRestApiStub());
    }

    /**
     * @return \Spryker\Client\CartCodesRestApi\CartCodeDeleter\CartCodeDeleterInterface
     */
    public function createCartCodeDeleter(): CartCodeDeleterInterface
    {
        return new CartCodeDeleter($this->createCartCodesRestApiStub());
    }

    /**
     * @return \Spryker\Client\CartCodesRestApi\Zed\CartCodesRestApiStubInterface
     */
    public function createCartCodesRestApiStub(): CartCodesRestApiStubInterface
    {
        return new CartCodesRestApiStub($this->getZedRequestClient());
    }

    /**
     * @return \Spryker\Client\CartCodesRestApi\Dependency\Client\CartCodesRestApiToZedRequestClientInterface
     */
    public function getZedRequestClient(): CartCodesRestApiToZedRequestClientInterface
    {
        return $this->getProvidedDependency(CartCodesRestApiDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
