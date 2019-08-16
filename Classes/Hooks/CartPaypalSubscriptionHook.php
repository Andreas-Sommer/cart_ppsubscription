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
namespace Belsignum\PaypalSubscription\Classes\Hooks;


use Extcode\Cart\Service\SessionHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Extcode\Cart\Utility\ParserUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Extcode\CartProducts\Domain\Repository\Product\ProductRepository;

class CartPaypalSubscriptionHook
{
	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var \Extcode\CartProducts\Domain\Repository\Product\ProductRepository
	 */
	protected $productRepository = null;

	/**
	 * @var ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var array
	 */
	protected $pluginSettings;

	/**
	 * Session Handler
	 *
	 * @var \Extcode\Cart\Service\SessionHandler
	 */
	protected $sessionHandler;

	/**
	 * @param array &$parameters
	 */
	public function showCartActionAfterCartWasLoaded(&$parameters, $refObj)
	{
		$this->objectManager = GeneralUtility::makeInstance(
			ObjectManager::class
		);
		$this->productRepository = $this->objectManager->get(
			ProductRepository::class
		);

		$orderProducts = $parameters['cart']->getProducts();
		if(\count($orderProducts))
		{
			/** @var \Extcode\Cart\Domain\Model\Cart\Product $firstOrderProduct */
			$firstOrderProduct = reset($orderProducts);

			/** @var \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product */
			$product = $this->productRepository->findByUid($firstOrderProduct->getProductId());

			if ($product->isSubscription())
			{
				$parserUtility = $this->objectManager->get(
					ParserUtility::class
				);
				$this->configurationManager = $this->objectManager->get(
					ConfigurationManager::class
				);
				$this->pluginSettings = $this->configurationManager->getConfiguration(
					ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
					'Cart'
				);
				$this->sessionHandler = $this->objectManager->get(
					SessionHandler::class
				);

				$payments = $parserUtility->parseServices(
					'Payment',
					$this->pluginSettings, $parameters['cart']
				);
				$payment = $this->getServiceByProvider($payments, 'PAYPAL_SUBSCRIPTION');
				$parameters['cart']->setPayment($payment);

				$this->sessionHandler->write(
					$parameters['cart'],
					$this->pluginSettings['settings']['cart']['pid']
				);
			}
		}
	}

	/**
	 * @param array $services
	 * @param int $provider
	 *
	 * @return mixed
	 */
	public function getServiceByProvider($services, $provider)
	{
		foreach ($services as $service) {
			if ($service->getProvider() === $provider) {
				return $service;
			}
		}

		return false;
	}
}
