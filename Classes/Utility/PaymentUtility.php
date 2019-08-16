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

use Extcode\Cart\Domain\Repository\CartRepository;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Extcode\CartProducts\Domain\Repository\Product\ProductRepository;

class PaymentUtility
{
	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var PersistenceManager
	 */
	protected $persistenceManager;

	/**
	 * @var ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var CartRepository
	 */
	#protected $cartRepository;

	/**
	 * @var array
	 */
	#protected $conf = [];

	/**
	 * @var array
	 */
	protected $cartConf = [];

	/**
	 * @var array
	 */
	protected $subscriptionConf = [];

	/**
	 * @var array
	 */
	#protected $paymentQuery = [];

	/**
	 * @var \Extcode\Cart\Domain\Model\Order\Item
	 */
	protected $orderItem = null;

	/**
	 * @var \Extcode\Cart\Domain\Model\Cart\Cart
	 */
	protected $cart = null;

	/**
	 * @var string
	 */
	protected $cartSHash = '';

	/**
	 * @var string
	 */
	protected $cartFHash = '';

	/**
	 * @var \Extcode\CartProducts\Domain\Repository\Product\ProductRepository
	 */
	protected $productRepository = null;

	/**
	 * @var \Belsignum\PaypalSubscription\Utility\SubscriptionUtility
	 */
	protected $subscriptionUtility =  null;

	/**
	 * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
	 */
	protected $itemRepository =  null;

	/**
	 * Intitialize
	 */
	public function __construct()
	{
		$this->objectManager = GeneralUtility::makeInstance(
			ObjectManager::class
		);
		$this->persistenceManager = $this->objectManager->get(
			PersistenceManager::class
		);
		$this->configurationManager = $this->objectManager->get(
			ConfigurationManager::class
		);
		$this->productRepository = $this->objectManager->get(
			ProductRepository::class
		);
		$this->itemRepository = $this->objectManager->get(
			ItemRepository::class
		);

		$this->cartConf = $this->configurationManager->getConfiguration(
			\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
			'Cart'
		);

		$this->subscriptionConf = $this->configurationManager->getConfiguration(
			\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
			'Paypalsubscription'
		);
	}

	/**
	 * handle PayPal Subscription payment
	 *
	 * @param array $params
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function handlePayment(array $params): array
	{
		$this->orderItem = $params['orderItem'];

		if ($this->orderItem->getPayment()->getProvider() === 'PAYPAL_SUBSCRIPTION') {
			$params['providerUsed'] = true;

			$this->cart = $params['cart'];

			$cart = $this->objectManager->get(
				\Extcode\Cart\Domain\Model\Cart::class
			);
			$cart->setOrderItem($this->orderItem);
			$cart->setCart($this->cart);
			$cart->setPid($this->cartConf['settings']['order']['pid']);

			$cartRepository = $this->objectManager->get(
				CartRepository::class
			);
			$cartRepository->add($cart);

			$this->persistenceManager->persistAll();

			$this->cartSHash = $cart->getSHash();
			$this->cartFHash = $cart->getFHash();

			$orderProducts = $this->cart->getProducts();
			// check if we have a subscription item, in this case more than one makes no sense as you are only able to buy only one subscription, so check just the first element.
			/** @var \Extcode\Cart\Domain\Model\Cart\Product $firstOrderProduct */
			$firstOrderProduct = reset($orderProducts);

			/** @var \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product */
			$product = $this->productRepository->findByUid($firstOrderProduct->getProductId());

			if($product->isSubscription())
			{
				$this->subscriptionUtility = new SubscriptionUtility($this->subscriptionConf['settings']);
				$subscription = $this->subscriptionUtility->createSubscription($this->orderItem, $product);

				$this->orderItem->setPaypalSubscriptionId($subscription->id);
				$this->orderItem->setAdditional([$subscription]);
				$this->itemRepository->update($this->orderItem);

				foreach ($subscription->links as $_ => $link)
				{
					if($link->rel === 'approve')
					{
						// redirect to approve
						header('Location: ' . $link->href);
					}
				}
			}
		}
		return [$params];
	}
}
