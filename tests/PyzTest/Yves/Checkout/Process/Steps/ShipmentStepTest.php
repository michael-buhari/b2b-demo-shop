<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Yves\Checkout\Process\Steps;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Shared\Shipment\ShipmentConstants;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use SprykerShop\Yves\CheckoutPage\CheckoutPageDependencyProvider;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsChecker;
use SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface;
use SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep;
use SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep\PostConditionChecker;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Checkout
 * @group Process
 * @group Steps
 * @group ShipmentStepTest
 * Add your own group annotations below this line
 */
class ShipmentStepTest extends Unit
{
    /**
     * @return void
     */
    public function testShipmentStepExecuteShouldTriggerPlugins(): void
    {
        $shipmentPluginMock = $this->createShipmentMock();
        $shipmentPluginMock->expects($this->once())->method('addToDataClass');

        $shipmentStepHandler = new StepHandlerPluginCollection();
        $shipmentStepHandler->add($shipmentPluginMock, CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER);
        $shipmentStep = $this->createShipmentStep($shipmentStepHandler);

        $quoteTransfer = new QuoteTransfer();

        $shipmentTransfer = new ShipmentTransfer();
        $shipmentTransfer->setShipmentSelection(CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER);
        $quoteTransfer->setShipment($shipmentTransfer);

        $shipmentStep->execute($this->createRequest(), $quoteTransfer);
    }

    /**
     * @return void
     */
    public function testShipmentPostConditionsShouldReturnTrueWhenShipmentSet(): void
    {
        $quoteTransfer = new QuoteTransfer();
        $expenseTransfer = new ExpenseTransfer();
        $expenseTransfer->setType(ShipmentConstants::SHIPMENT_EXPENSE_TYPE);
        $quoteTransfer->addExpense($expenseTransfer);

        $shipmentStep = $this->createShipmentStep(new StepHandlerPluginCollection());

        $this->assertTrue($shipmentStep->postCondition($quoteTransfer));
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $shipmentPlugins
     *
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\ShipmentStep
     */
    protected function createShipmentStep(StepHandlerPluginCollection $shipmentPlugins): ShipmentStep
    {
        return new ShipmentStep(
            $this->createCalculationClientMock(),
            $shipmentPlugins,
            $this->createShipmentStepPostConditionCheckerMock(),
            $this->createGiftCardItemsCheckerMock(),
            CheckoutPageDependencyProvider::PLUGIN_SHIPMENT_STEP_HANDLER,
            'escape_route'
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest(): Request
    {
        return Request::createFromGlobals();
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface
     */
    protected function createCalculationClientMock(): CheckoutPageToCalculationClientInterface
    {
        $calculationClientMock = $this->createMock(CheckoutPageToCalculationClientInterface::class);
        $calculationClientMock->method('recalculate')
            ->willReturnArgument(0);

        return $calculationClientMock;
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface
     */
    protected function createShipmentMock(): StepHandlerPluginInterface
    {
        return $this->createMock(StepHandlerPluginInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface
     */
    protected function createShipmentStepPostConditionCheckerMock(): PostConditionCheckerInterface
    {
        $calculationMock = $this->getMockBuilder(PostConditionChecker::class)->getMock();

        return $calculationMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerShop\Yves\CheckoutPage\GiftCard\GiftCardItemsCheckerInterface
     */
    protected function createGiftCardItemsCheckerMock(): GiftCardItemsCheckerInterface
    {
        $calculationMock = $this->getMockBuilder(GiftCardItemsChecker::class)->getMock();

        return $calculationMock;
    }
}
