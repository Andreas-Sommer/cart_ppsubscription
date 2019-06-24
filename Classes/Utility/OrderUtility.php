<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 19.06.2019
 * Time: 15:35
 */

namespace Belsignum\PaypalSubscription\Utility;

use Belsignum\PaypalSubscription\Domain\Repository\Product\ProductRepository;
use Extcode\Cart\Domain\Model\Order\ProductAdditional;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Belsignum\PaypalSubscription\Domain\Model\Sequence;
use TYPO3\CMS\Extbase\Property\TypeConverter\ArrayConverter;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;

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
