<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 14:16
 */

namespace Belsignum\PaypalSubscription\Domain\Model\Product;

class Product extends \Extcode\CartProducts\Domain\Model\Product\Product
{

	/**
	 * isSubscription
	 *
	 * @var bool
	 */
	protected $isSubscription = FALSE;

	/**
	 * @return bool
	 */
	public function isSubscription(): bool
	{
		return $this->isSubscription;
	}

	/**
	 * @param bool $isSubscription
	 */
	public function setIsSubscription(bool $isSubscription): void
	{
		$this->isSubscription = $isSubscription;
	}


}
