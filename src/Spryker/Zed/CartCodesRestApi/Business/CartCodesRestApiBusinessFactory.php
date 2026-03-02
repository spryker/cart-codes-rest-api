<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartCodesRestApi\Business;

use Spryker\Zed\CartCodesRestApi\Business\CartCodeAdder\CartCodeAdder;
use Spryker\Zed\CartCodesRestApi\Business\CartCodeAdder\CartCodeAdderInterface;
use Spryker\Zed\CartCodesRestApi\Business\CartCodeRemover\CartCodeRemover;
use Spryker\Zed\CartCodesRestApi\Business\CartCodeRemover\CartCodeRemoverInterface;
use Spryker\Zed\CartCodesRestApi\CartCodesRestApiDependencyProvider;
use Spryker\Zed\CartCodesRestApi\Dependency\Facade\CartCodesRestApiToCartCodeFacadeInterface;
use Spryker\Zed\CartCodesRestApi\Dependency\Facade\CartCodesRestApiToCartsRestApiFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Spryker\Zed\CartCodesRestApi\CartCodesRestApiConfig getConfig()
 */
class CartCodesRestApiBusinessFactory extends AbstractBusinessFactory
{
    public function createCartCodeAdder(): CartCodeAdderInterface
    {
        return new CartCodeAdder(
            $this->getCartCodeFacade(),
            $this->getCartsRestApiFacade(),
        );
    }

    public function createCartCodeRemover(): CartCodeRemoverInterface
    {
        return new CartCodeRemover(
            $this->getCartCodeFacade(),
            $this->getCartsRestApiFacade(),
        );
    }

    public function getCartCodeFacade(): CartCodesRestApiToCartCodeFacadeInterface
    {
        return $this->getProvidedDependency(CartCodesRestApiDependencyProvider::FACADE_CART_CODE);
    }

    public function getCartsRestApiFacade(): CartCodesRestApiToCartsRestApiFacadeInterface
    {
        return $this->getProvidedDependency(CartCodesRestApiDependencyProvider::FACADE_CARTS_REST_API);
    }
}
