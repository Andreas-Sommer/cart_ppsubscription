<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 25.06.2019
 * Time: 16:39
 */

namespace Belsignum\PaypalSubscription\Domain\Model\Cart;

class Payment extends \Extcode\Cart\Domain\Model\Cart\AbstractService
{

	/**
	 * @return \Extcode\Cart\Domain\Model\Cart\Cart
	 */
	public function getCart(): \Extcode\Cart\Domain\Model\Cart\Cart
	{
		return $this->cart;
	}
}
