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
namespace Belsignum\PaypalSubscription\Utility;

use Belsignum\PaypalSubscription\Domain\Repository\Product\ProductRepository;
use Extcode\Cart\Domain\Model\Order\ProductAdditional;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Belsignum\PaypalSubscription\Domain\Model\Sequence;

class OrderUtility
{

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager;
	 */
	protected $objectManager;

	/**
	 * @var \Belsignum\PaypalSubscription\Domain\Repository\Product\ProductRepository
	 */
	protected $productProductRepository;

	public function __construct()
	{
		$this->objectManager = new ObjectManager();
		$this->productProductRepository = $this->objectManager->get(
			ProductRepository::class
		);
	}

	/**
	 * add the paypal product fields as productAdditional
	 *
	 * @param array $params
	 */
	public function addProductAdditionalData(array $params):void
	{
		/** @var \Extcode\Cart\Domain\Model\Order\Product $orderProduct */
		$orderProduct = $params['orderProduct'];

		$productId = $params['cartProduct']->getProductId();

		/** @var \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product */
		$product = $this->productProductRepository->findByUid($productId);

		if($product->isSubscription())
		{
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['bool', 'isSubscription', $product->isSubscription() ? 'TRUE' : 'FALSE'])
			);
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['string', 'paypalType', $product->getPaypalType()])
			);
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['string', 'paypalCategory', $product->getPaypalCategory()])
			);
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['string', 'paypalProductId', $product->getPaypalProductId()])
			);
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['string', 'paypalPlanId', $product->getPaypalPlanId()])
			);
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['string', 'paypalSetupFailure', $product->getPaypalSetupFailure()])
			);
			$orderProduct->addProductAdditional(
				$this->generateProductAdditional(['int', 'paypalFailureThreshold', (string) $product->getPaypalFailureThreshold() ?: 'zero'])
			);
			$i = 1;
			foreach ($product->getPaypalSequence() as $_ => $paypalSequence)
			{
				/** @var ProductAdditional $productAdditional */
				$productAdditional = $this->generateProductAdditional([
					Sequence::class, 'paypalSequence',
					(string) $i,
					serialize($paypalSequence)
				]);
				$orderProduct->addProductAdditional(
					$productAdditional
				);
				$i++;
			}

			// some how this is always given as "CartProducts" -> fix it
			$orderProduct->setProductType($product->getProductType());
			$params['orderProduct'] = $orderProduct;
		}
	}

	/**
	 * generate the productAdditional
	 *
	 * @param array $data
	 *
	 * @return \Extcode\Cart\Domain\Model\Order\ProductAdditional
	 */
	protected function generateProductAdditional(array $data):ProductAdditional
	{
		$productAdditional = new ProductAdditional(
			$data[0],		// type
			$data[1],		// key
			$data[2],		// value
			$data[3]		// data to json
		);
		return $productAdditional;
	}
}
