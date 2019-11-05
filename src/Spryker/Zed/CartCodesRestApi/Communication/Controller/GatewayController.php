<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartCodesRestApi\Communication\Controller;

use Generated\Shared\Transfer\AddCandidateRequestTransfer;
use Generated\Shared\Transfer\CartCodeOperationResultTransfer;
use Generated\Shared\Transfer\RemoveCodeRequestTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\CartCodesRestApi\Business\CartCodesRestApiFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\AddCandidateRequestTransfer $addCandidateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CartCodeOperationResultTransfer
     */
    public function addCandidateAction(AddCandidateRequestTransfer $addCandidateRequestTransfer): CartCodeOperationResultTransfer
    {
        $quoteTransfer = $addCandidateRequestTransfer->getQuote();
        $voucherCode = $addCandidateRequestTransfer->getVoucherCode();

        return $this->getFacade()->addCandidate($quoteTransfer, $voucherCode);
    }

    /**
     * @param \Generated\Shared\Transfer\RemoveCodeRequestTransfer $removeCodeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CartCodeOperationResultTransfer
     */
    public function removeCodeAction(RemoveCodeRequestTransfer $removeCodeRequestTransfer): CartCodeOperationResultTransfer
    {
        $quoteTransfer = $removeCodeRequestTransfer->getQuote();
        $idDiscount = $removeCodeRequestTransfer->getIdDiscount();

        return $this->getFacade()->removeCode($quoteTransfer, $idDiscount);
    }
}
