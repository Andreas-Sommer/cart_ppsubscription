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
	 * paypalType
	 *
	 * @var string
	 */
	protected $paypalType = '';

	/**
	 * paypalCategory
	 *
	 * @var string
	 */
	protected $paypalCategory = '';

	/**
	 * paypalProductId
	 *
	 * @var string
	 */
	protected $paypalProductId = '';

	/**
	 * paypalPlanId
	 *
	 * @var string
	 */
	protected $paypalPlanId = '';

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

	/**
	 * @return string
	 */
	public function getPaypalType(): string
	{
		return $this->paypalType;
	}

	/**
	 * @param string $paypalType
	 */
	public function setPaypalType(string $paypalType): void
	{
		$this->paypalType = $paypalType;
	}

	/**
	 * @return string
	 */
	public function getPaypalCategory(): string
	{
		return $this->paypalCategory;
	}

	/**
	 * @param string $paypalCategory
	 */
	public function setPaypalCategory(string $paypalCategory): void
	{
		$this->paypalCategory = $paypalCategory;
	}

	/**
	 * @return string
	 */
	public function getPaypalProductId(): string
	{
		return $this->paypalProductId;
	}

	/**
	 * @param string $paypalProductId
	 */
	public function setPaypalProductId(string $paypalProductId): void
	{
		$this->paypalProductId = $paypalProductId;
	}

	/**
	 * @return string
	 */
	public function getPaypalPlanId(): string
	{
		return $this->paypalPlanId;
	}

	/**
	 * @param string $paypalPlanId
	 */
	public function setPaypalPlanId(string $paypalPlanId): void
	{
		$this->paypalPlanId = $paypalPlanId;
	}



}
