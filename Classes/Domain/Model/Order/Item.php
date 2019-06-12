<?php
namespace Belsignum\PaypalSubscription\Domain\Model\Order;


/**
 * Order Item Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Item extends \Extcode\Cart\Domain\Model\Order\Item
{
    /**
     * Paypal Subscription ID
     *
     * @var string
     */
    protected $paypalSubscriptionId;

	/**
	 * @return string
	 */
	public function getPaypalSubscriptionId(): string
	{
		return $this->paypalSubscriptionId;
	}

	/**
	 * @param string $paypalSubscriptionId
	 */
	public function setPaypalSubscriptionId(string $paypalSubscriptionId): void
	{
		$this->paypalSubscriptionId = $paypalSubscriptionId;
	}
}
