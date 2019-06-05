<?php
namespace Belsignum\PaypalSubscription\Classes\Hooks;


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

				$payments = $parserUtility->parseServices('Payment', $this->pluginSettings, $parameters['cart']);
				$payment = $this->getServiceByProvider($payments, 'PAYPAL_SUBSCRIPTION');
				$parameters['cart']->setPayment($payment);
			}
		}

	}

	/**
	 * @param array $services
	 * @param int $serviceId
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
