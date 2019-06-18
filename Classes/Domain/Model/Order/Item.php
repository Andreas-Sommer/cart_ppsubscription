<?php
namespace Belsignum\PaypalSubscription\Domain\Model\Order;
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 15:45
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
