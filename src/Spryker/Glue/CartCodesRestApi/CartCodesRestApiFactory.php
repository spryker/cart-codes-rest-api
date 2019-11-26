<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi;

use Spryker\Glue\CartCodesRestApi\Dependency\RestApiResource\CartCodesRestApiToCartsRestApiResourceInterface;
use Spryker\Glue\CartCodesRestApi\Processor\CartCodeAdder\CartCodeAdder;
use Spryker\Glue\CartCodesRestApi\Processor\CartCodeAdder\CartCodeAdderInterface;
use Spryker\Glue\CartCodesRestApi\Processor\CartCodeRemover\CartCodeRemover;
use Spryker\Glue\CartCodesRestApi\Processor\CartCodeRemover\CartCodeRemoverInterface;
use Spryker\Glue\CartCodesRestApi\Processor\Expander\DiscountByQuoteResourceRelationshipExpander;
use Spryker\Glue\CartCodesRestApi\Processor\Expander\DiscountByQuoteResourceRelationshipExpanderInterface;
use Spryker\Glue\CartCodesRestApi\Processor\Expander\PromotionItemByQuoteResourceRelationshipExpander;
use Spryker\Glue\CartCodesRestApi\Processor\Expander\PromotionItemByQuoteResourceRelationshipExpanderInterface;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\CartCodeMapper;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\CartCodeMapperInterface;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapper;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapperInterface;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\PromotionItemMapper;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\PromotionItemMapperInterface;
use Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder\CartCodeRestResponseBuilder;
use Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder\CartCodeRestResponseBuilderInterface;
use Spryker\Glue\Kernel\AbstractFactory;

/**
 * @method \Spryker\Client\CartCodesRestApi\CartCodesRestApiClientInterface getClient()
 * @method \Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig getConfig()
 */
class CartCodesRestApiFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\CartCodeAdder\CartCodeAdderInterface
     */
    public function createCartCodeAdder(): CartCodeAdderInterface
    {
        return new CartCodeAdder(
            $this->getClient(),
            $this->createCartCodeRestResponseBuilder()
        );
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\CartCodeRemover\CartCodeRemoverInterface
     */
    public function createCartCodeRemover(): CartCodeRemoverInterface
    {
        return new CartCodeRemover(
            $this->getClient(),
            $this->createCartCodeRestResponseBuilder()
        );
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\RestResponseBuilder\CartCodeRestResponseBuilderInterface
     */
    public function createCartCodeRestResponseBuilder(): CartCodeRestResponseBuilderInterface
    {
        return new CartCodeRestResponseBuilder(
            $this->getResourceBuilder(),
            $this->createCartCodeMapper(),
            $this->getCartsRestApiResource()
        );
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\Mapper\CartCodeMapperInterface
     */
    public function createCartCodeMapper(): CartCodeMapperInterface
    {
        return new CartCodeMapper($this->getConfig());
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapperInterface
     */
    public function createDiscountMapper(): DiscountMapperInterface
    {
        return new DiscountMapper();
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\Mapper\PromotionItemMapperInterface
     */
    public function createPromotionItemMapper(): PromotionItemMapperInterface
    {
        return new PromotionItemMapper();
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\Expander\DiscountByQuoteResourceRelationshipExpanderInterface
     */
    public function createDiscountByQuoteResourceRelationshipExpander(): DiscountByQuoteResourceRelationshipExpanderInterface
    {
        return new DiscountByQuoteResourceRelationshipExpander(
            $this->getResourceBuilder(),
            $this->createDiscountMapper()
        );
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Processor\Expander\PromotionItemByQuoteResourceRelationshipExpanderInterface
     */
    public function createPromotionItemByQuoteResourceRelationshipExpander(): PromotionItemByQuoteResourceRelationshipExpanderInterface
    {
        return new PromotionItemByQuoteResourceRelationshipExpander(
            $this->getResourceBuilder(),
            $this->createPromotionItemMapper()
        );
    }

    /**
     * @return \Spryker\Glue\CartCodesRestApi\Dependency\RestApiResource\CartCodesRestApiToCartsRestApiResourceInterface
     */
    public function getCartsRestApiResource(): CartCodesRestApiToCartsRestApiResourceInterface
    {
        return $this->getProvidedDependency(CartCodesRestApiDependencyProvider::CARTS_REST_API_RESOURCE);
    }
}
