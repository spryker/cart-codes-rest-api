<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CartCodesRestApi;

use Codeception\Actor;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 * @method \Spryker\Zed\CartCodesRestApi\Business\CartCodesRestApiFacade getFacade()
 *
 * @SuppressWarnings(PHPMD)
 */
class CartCodesRestApiBusinessTester extends Actor
{
    use _generated\CartCodesRestApiBusinessTesterActions;

    public const CODE = 'testCode1';

    public const NON_EXISTENT_CODE = 'testCode2';

    public const NON_EXISTENT_ID_DISCOUNT = 7777;

    public const TEST_QUOTE_UUID = 'test-quote-uuid';

    public const TEST_CUSTOMER_REFERENCE = 'DE--666';

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function prepareQuoteTransfer(): QuoteTransfer
    {
        return (new QuoteBuilder(
            [
                'uuid' => static::TEST_QUOTE_UUID,
                'customerReference' => static::TEST_CUSTOMER_REFERENCE,
            ]
        ))->build();
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function havePersistentQuoteWithVouchers(): QuoteTransfer
    {
        $customerTransfer = $this->haveCustomer();

        $discountTransfer = (new DiscountTransfer())
            ->setVoucherCode(static::CODE);

        return $this->havePersistentQuote([
            QuoteTransfer::UUID => uniqid('uuid'),
            QuoteTransfer::CUSTOMER => $customerTransfer,
            QuoteTransfer::VOUCHER_DISCOUNTS => [$discountTransfer->toArray()],
            QuoteTransfer::STORE => [
                StoreTransfer::NAME => 'DE',
                StoreTransfer::ID_STORE => 1,
            ],
        ]);
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function havePersistentQuoteWithoutVouchers(): QuoteTransfer
    {
        return $this->havePersistentQuote([
            QuoteTransfer::UUID => uniqid('uuid', true),
            QuoteTransfer::CUSTOMER => $this->haveCustomer(),
        ]);
    }
}
