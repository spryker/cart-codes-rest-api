<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CartCodesRestApi\Processor\Expander;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestDiscountsAttributesTransfer;
use Spryker\Glue\CartCodesRestApi\CartCodesRestApiConfig;
use Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapperInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class DiscountByCartResourceRelationshipExpander implements DiscountByCartResourceRelationshipExpanderInterface
{
    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapperInterface
     */
    protected $discountMapperInterface;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapperInterface $discountMapperInterface
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder,
        DiscountMapperInterface $discountMapperInterface
    ) {
        $this->restResourceBuilder = $restResourceBuilder;
        $this->discountMapperInterface = $discountMapperInterface;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface[] $resources
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return void
     */
    public function addResourceRelationships(array $resources, RestRequestInterface $restRequest): void
    {
        foreach ($resources as $resource) {
            /**
             * @var \Generated\Shared\Transfer\QuoteTransfer|null $payload
             */
            $payload = $resource->getPayload();
            if ($payload === null || !($payload instanceof QuoteTransfer)) {
                continue;
            }

            $discountTransfers = array_merge(
                $payload->getVoucherDiscounts()->getArrayCopy(),
                $payload->getCartRuleDiscounts()->getArrayCopy()
            );

            if (!count($discountTransfers)) {
                continue;
            }

            $this->addDiscountResourceRelationship($discountTransfers, $resource);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\DiscountTransfer[] $discountTransfers
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface $resource
     *
     * @return void
     */
    protected function addDiscountResourceRelationship(
        array $discountTransfers,
        RestResourceInterface $resource
    ): void {
        foreach ($discountTransfers as $discountTransfer) {
            $restDiscountsAttributesTransfer = $this->discountMapperInterface
                ->mapDiscountDataToRestDiscountsAttributesTransfer(
                    $discountTransfer,
                    new RestDiscountsAttributesTransfer()
                );

            $discountResource = $this->restResourceBuilder->createRestResource(
                CartCodesRestApiConfig::RESOURCE_DISCOUNTS,
                $discountTransfer->getIdDiscount(),
                $restDiscountsAttributesTransfer
            );

            $resource->addRelationship($discountResource);
        }
    }
}