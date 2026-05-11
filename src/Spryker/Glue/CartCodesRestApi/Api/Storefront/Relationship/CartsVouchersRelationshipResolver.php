<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\CartCodesRestApi\Api\Storefront\Relationship;

use Generated\Api\Storefront\VouchersStorefrontResource;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\RestDiscountsAttributesTransfer;
use Spryker\ApiPlatform\Relationship\AbstractRelationshipResolver;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Service\Serializer\SerializerServiceInterface;

/**
 * Builds `Vouchers` sub-resources from `voucherDiscounts` carried on a `Carts`/`GuestCarts`
 * parent resource. Mirrors the legacy {@see \Spryker\Glue\CartCodesRestApi\Processor\Mapper\DiscountMapper}
 * behavior: each `DiscountTransfer` is hydrated into a `RestDiscountsAttributesTransfer` and
 * passed through the registered {@see \Spryker\Glue\CartCodesRestApiExtension\Dependency\Plugin\DiscountMapperPluginInterface}
 * chain (e.g. `DiscountPromotionDiscountMapperPlugin` populates `discountPromotionAbstractSku`/
 * `discountPromotionQuantity`) before being denormalized into the Storefront resource.
 */
class CartsVouchersRelationshipResolver extends AbstractRelationshipResolver
{
    /**
     * @param array<\Spryker\Glue\CartCodesRestApiExtension\Dependency\Plugin\DiscountMapperPluginInterface> $discountMapperPlugins
     */
    public function __construct(
        protected SerializerServiceInterface $serializer,
        #[Plugins(dependencyProviderMethod: 'getDiscountMapperPlugins')]
        protected array $discountMapperPlugins = [],
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\VouchersStorefrontResource>
     */
    protected function resolveRelationship(): array
    {
        $resources = [];

        foreach ($this->getParentResources() as $parent) {
            $voucherDiscounts = $parent->voucherDiscounts ?? [];

            foreach ($voucherDiscounts as $discountTransfer) {
                if (!$discountTransfer instanceof DiscountTransfer) {
                    continue;
                }

                $resources[] = $this->mapDiscountToResource($discountTransfer);
            }
        }

        return $resources;
    }

    protected function mapDiscountToResource(DiscountTransfer $discountTransfer): VouchersStorefrontResource
    {
        $restDiscountsAttributesTransfer = $this->buildRestDiscountsAttributesTransfer($discountTransfer);

        $resource = $this->serializer->denormalize(
            $restDiscountsAttributesTransfer->toArray(true, true),
            VouchersStorefrontResource::class,
        );

        // `idDiscount` is the JSON:API identifier for vouchers but is absent from
        // `RestDiscountsAttributesTransfer`, so it must be carried from the source transfer.
        $resource->idDiscount = $discountTransfer->getIdDiscount();

        return $resource;
    }

    protected function buildRestDiscountsAttributesTransfer(DiscountTransfer $discountTransfer): RestDiscountsAttributesTransfer
    {
        $restDiscountsAttributesTransfer = (new RestDiscountsAttributesTransfer())
            ->fromArray($discountTransfer->toArray(), true)
            ->setCode($discountTransfer->getVoucherCode())
            ->setExpirationDateTime($discountTransfer->getValidTo());

        foreach ($this->discountMapperPlugins as $discountMapperPlugin) {
            $restDiscountsAttributesTransfer = $discountMapperPlugin->mapDiscountTransferToRestDiscountsAttributesTransfer(
                $discountTransfer,
                $restDiscountsAttributesTransfer,
            );
        }

        return $restDiscountsAttributesTransfer;
    }
}
