<?php
/**
 * Typo3 Extension paypal_subscription
 * PayPal Subscriptions based on extensions cart and cart_products to enable recurring transactions
 * Copyright (C) 2019  Andreas Sommer <sommer@belsignum.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
	 * paypalSetupFailure
	 *
	 * @var string
	 */
	protected $paypalSetupFailure = '';

	/**
	 * paypalFailureThreshold
	 *
	 * @var int
	 */
	protected $paypalFailureThreshold = 0;

	/**
	 * paypalSequence
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Belsignum\PaypalSubscription\Domain\Model\Sequence>
	 * @lazy
	 */
	protected $paypalSequence = null;

	/**
	 * __construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->paypalSequence = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * @return bool
	 */
	public function isSubscription(): bool
	{
		return $this->isSubscription;
	}

	/**
	 * @return bool
	 */
	public function getIsSubscription(): bool
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

	/**
	 * @return string
	 */
	public function getPaypalSetupFailure(): string
	{
		return $this->paypalSetupFailure;
	}

	/**
	 * @param string $paypalSetupFailure
	 */
	public function setPaypalSetupFailure(string $paypalSetupFailure): void
	{
		$this->paypalSetupFailure = $paypalSetupFailure;
	}

	/**
	 * @return int
	 */
	public function getPaypalFailureThreshold(): int
	{
		return $this->paypalFailureThreshold;
	}

	/**
	 * @param int $paypalFailureThreshold
	 */
	public function setPaypalFailureThreshold(int $paypalFailureThreshold): void
	{
		$this->paypalFailureThreshold = $paypalFailureThreshold;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Belsignum\PaypalSubscription\Domain\Model\Sequence>
	 */
	public function getPaypalSequence(
	): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	{
		return $this->paypalSequence;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Belsignum\PaypalSubscription\Domain\Model\Sequence> $paypalSequence
	 */
	public function setPaypalSequence(
		\TYPO3\CMS\Extbase\Persistence\ObjectStorage $paypalSequence
	): void {
		$this->paypalSequence = $paypalSequence;
	}

}
