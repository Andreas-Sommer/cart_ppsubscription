<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 23.05.2019
 * Time: 16:01
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
