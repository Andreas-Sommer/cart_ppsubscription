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
namespace Belsignum\PaypalSubscription\Domain\Model;

class Sequence extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
	/**
	 * type
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * intervalUnit
	 *
	 * @var string
	 */
	protected $intervalUnit = '';

	/**
	 * intervalCount
	 *
	 * @var int
	 */
	protected $intervalCount = 1;

	/**
	 * totalCycles
	 *
	 * @var int
	 */
	protected $totalCycles = 1;

	/**
	 * price
	 *
	 * @var double
	 */
	protected $price = 0.00;

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getIntervalUnit(): string
	{
		return $this->intervalUnit;
	}

	/**
	 * @param string $intervalUnit
	 */
	public function setIntervalUnit(string $intervalUnit): void
	{
		$this->intervalUnit = $intervalUnit;
	}

	/**
	 * @return int
	 */
	public function getIntervalCount(): int
	{
		return $this->intervalCount;
	}

	/**
	 * @param int $intervalCount
	 */
	public function setIntervalCount(int $intervalCount): void
	{
		$this->intervalCount = $intervalCount;
	}

	/**
	 * @return int
	 */
	public function getTotalCycles(): int
	{
		return $this->totalCycles;
	}

	/**
	 * @param int $totalCycles
	 */
	public function setTotalCycles(int $totalCycles): void
	{
		$this->totalCycles = $totalCycles;
	}

	/**
	 * @return float
	 */
	public function getPrice(): float
	{
		return $this->price;
	}

	/**
	 * @param float $price
	 */
	public function setPrice(float $price): void
	{
		$this->price = $price;
	}

}
