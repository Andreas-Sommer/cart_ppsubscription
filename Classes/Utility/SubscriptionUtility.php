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

use Belsignum\PaypalSubscription\Domain\Model\Product\Product;
use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SubscriptionUtility
{
	protected const PAYPAL_API_SANDBOX = 'https://api.sandbox.paypal.com/v1/';
	protected const PAYPAL_API_LIVE = 'https://api.paypal.com/v1/';
	protected const SEGMENT_OAUTH = 'oauth2/token';
	protected const SEGMENT_PRODUCTS = 'catalogs/products';
	protected const SEGMENT_PLANS = 'billing/plans';
	protected const SEGMENT_SUBSCRIPTIONS = 'billing/subscriptions';

	/**
	 * paypal client id
	 * @var string
	 */
	protected $client = '';

	/**
	 * paypal secret
	 * @var string
	 */
	protected $secret = '';

	/**
	 * @var int
	 */
	protected $sandbox = 0;

	/**
	 * @var array
	 */
	protected $currencies = [];

	/**
	 * @var array
	 */
	protected $taxClasses = [];

	/**
	 * @var array
	 */
	protected $scope = [];

	/**
	 * @var string
	 */
	protected $accessToken = '';

	/**
	 * @var string
	 */
	protected $tokenType = '';

	/**
	 * @var string
	 */
	protected $appId = '';

	/**
	 * @var int
	 */
	protected $expiresIn = 0;

	/**
	 * @var string
	 */
	protected $nonce = '';

	/**
	 * @var int
	 */
	protected $tstamp = 0;

	/**
	 * @var array
	 */
	protected $subscriptionSettings = null;

	/**
	 * SubscriptionUtility constructor.
	 *
	 * @param array $settings
	 */
	public function __construct(array $settings)
	{
		$this->client = $settings['client'];
		$this->secret = $settings['secret'];
		$this->sandbox = (int) $settings['sandbox'];
		$this->currencies = $settings['tx_cart']['settings']['currencies'];
		$this->taxClasses = $settings['tx_cart']['taxClasses'];
		$this->subscriptionSettings = $settings;

		$this->requestAccessToken();
	}

	/**
	 * request access token for further paypal requests
	 * @return void
	 */
	public function requestAccessToken():void
	{
		$url = $this->getUrl(self::SEGMENT_OAUTH);
		$header = [
			'Accept: application/json',
			'Accept-Language: en_US'
		];
		$credentials = $this->client . ':' . $this->secret;
		$customFields = 'grant_type=client_credentials';

		try
		{
			$response = $this->curlRequest($url, $header, $customFields,$credentials);
		}
		catch(\Exception $exception)
		{
			throw new $exception;
		}

		$this->scope = GeneralUtility::trimExplode(' ', $response->scope);
		$this->accessToken = $response->access_token;
		$this->tokenType = $response->token_type;
		$this->appId = $response->app_id;
		$this->expiresIn = $response->expires_in;
		$this->nonce = $response->nonce;
		$this->tstamp = time();
	}

	/**
	 * create a paypal product
	 *
	 * @param \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function createCatalogProduct(Product $product)
	{
		$url = $this->getUrl(self::SEGMENT_PRODUCTS);

		$header = [
			'Content-Type: application/json',
			'Authorization: ' . $this->tokenType . ' ' . $this->accessToken,
			'PayPal-Request-Id: PRODUCT-' . $product->getUid() . '-' . $product->getSku(),
		];

		$customFields = [
			'name' => $product->getTitle(),
			'description' => $product->getDescription() ?: $product->getTitle(),
			'type' => strtoupper($product->getPaypalType()),
			'category' => strtoupper($product->getPaypalCategory()),
			#'image_url' => 'https://example.com/streaming.jpg',
			#'home_url' => 'https://example.com/home',
		];

		return $this->curlRequest($url, $header, $customFields);
	}

	/**
	 * create billing plan
	 *
	 * @param \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product
	 * @return mixed
	 * @throws \Exception
	 */
	public function createBillingPlan(Product $product)
	{
		$url = $this->getUrl(self::SEGMENT_PLANS);

		$header = [
			'Accept: application/json',
			'Authorization: ' . $this->tokenType . ' ' . $this->accessToken,
			'PayPal-Request-Id: PLAN-' . $product->getUid() . '-' . $product->getSku(),
			'Prefer: return=representation',
			'Content-Type: application/json',
		];

		$currency = $this->currencies[$this->currencies['default']]['code'];
		$taxRate = $this->taxClasses[$product->getTaxClassId()]['value'];

		$setupFee = $setupFee = [
			'value' => '0',
			'currency_code' => $currency
		];
		$sequences = [];
		if($product->getPaypalSequence()->count())
		{
			$i = 1;
			/** @var \Belsignum\PaypalSubscription\Domain\Model\Sequence $sequence */
			foreach ($product->getPaypalSequence() as $_ => $sequence)
			{
				if($sequence->getType() === 'setup_fee')
				{
					$setupFee['value'] = (string) $sequence->getPrice();
				}
				else
				{
					$sequences[] = [
						'frequency' => [
							'interval_unit' => $sequence->getIntervalUnit(),
							'interval_count' => $sequence->getIntervalCount(),
						],
						'tenure_type' => strtoupper($sequence->getType()),
						'sequence' => $i,
						'total_cycles' => (string) $sequence->getTotalCycles(),
						'pricing_scheme' => [
							'fixed_price' => [
								'value' => (string) ($sequence->getType() === 'trial' ? 0 : $sequence->getPrice()),
								'currency_code' => $currency,
							],
						]
					];
					$i++;
				}

			}
		}

		$customFields = [
			'product_id' => $product->getPaypalProductId(),
			'name' => $product->getTitle() . ' Plan',
			'description' => $product->getDescription() ?: $product->getTitle(), // empty description would cause failure, use title in this case
			'billing_cycles' => $sequences,
			'payment_preferences' => [
				'auto_bill_outstanding' => TRUE,
				'setup_fee_failure_action' => strtoupper($product->getPaypalSetupFailure()),
				'payment_failure_threshold' => $product->getPaypalFailureThreshold(),
				'setup_fee' => $setupFee
			],
			'taxes' => [
				'percentage' => $taxRate,
				'inclusive' => !$product->getIsNetPrice(),
			],
		];

		return $this->curlRequest($url, $header, $customFields);
	}

	/**
	 * create subscription
	 *
	 * @param \Extcode\Cart\Domain\Model\Order\Item                      $orderItem
	 * @param \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function createSubscription(Item $orderItem, Product $product)
	{
		$billingAddress = $orderItem->getBillingAddress();
		$url = $this->getUrl(self::SEGMENT_SUBSCRIPTIONS);

		$header = [
			'Accept: application/json',
			'Authorization: ' . $this->tokenType . ' ' . $this->accessToken,
			'PayPal-Request-Id: Subscription-' . $product->getUid() . '-' . $product->getSku() . '-' . time(),
			'Prefer: return=representation',
			'Content-Type: application/json',
		];

		$customFields = [
			'plan_id' => $product->getPaypalPlanId(),
			'start_time' => date('Y-m-d') . 'T' . date('H:m:i') . 'Z' , #. date('Z'),
			'subscriber' => [
				'name' => [
					'given_name' => $billingAddress->getFirstName(),
					'surname' => $billingAddress->getLastName()
				],
				'email_address' => $billingAddress->getEmail()
			],
			'auto_renewal' => true,
			'application_context' => [
				'shipping_preference' => 'NO_SHIPPING',
				'user_action' => 'SUBSCRIBE_NOW',
				'payment_method' => [
					'payer_selected' => 'PAYPAL',
					'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
				],
				'return_url' => $this->getReturnUrl($orderItem),
				'cancel_url' => GeneralUtility::getIndpEnv('HTTP_REFERER')
      		]
		];

		try
		{
			return $this->curlRequest($url, $header, $customFields);
		}
		catch(\Exception $exception)
		{
			throw $exception;
		}
	}

	/**
	 * cancel subscription
	 *
	 * @param string $subscriptionId
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function cancelSubscription($subscriptionId)
	{
		$url = $this->getUrl(self::SEGMENT_SUBSCRIPTIONS) . '/' .$subscriptionId . '/cancel';
		$header = [
			'Authorization: ' . $this->tokenType . ' ' . $this->accessToken,
			'Content-Type: application/json',
		];

		try
		{
			return $this->curlRequest($url, $header);
		}
		catch(\Exception $exception)
		{
			throw $exception;
		}
	}

	/**
	 * get paypal product catalog
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function getCatalog()
	{
		$customFields = [
			'page' => 1,
			'page_size' => 100,
			'total_required' => 'true',
		];

		$url = $this->getUrl(self::SEGMENT_PRODUCTS, $customFields);

		$header = [
			'Content-Type: application/json',
			'Authorization: ' . $this->tokenType . ' ' . $this->accessToken,
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$result = curl_exec($ch);
		if(empty($result))
		{
			throw new \Exception('Error: No response.');
		}

		$response = json_decode($result);
		if(!$response->id)
		{
			throw new \Exception($response->message);
		}
		return $response;
	}

	/**
	 * generate url
	 *
	 * @param string $segment
	 * @param array $customFields
	 * @return string
	 */
	protected function getUrl(string $segment, array $customFields = null):string
	{
		$url = $this->sandbox === 1 ? self::PAYPAL_API_SANDBOX : self::PAYPAL_API_LIVE;
		$url .= $segment;

		if($customFields)
		{
			$url .= '?' . http_build_query($customFields);
		}
		return $url;
	}

	/**
	 * @param string      $url
	 * @param array       $header
	 * @param null        $customFields
	 * @param string|null $credentials
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function curlRequest(string $url, array $header, $customFields = null, string $credentials = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		if($customFields && \is_array($customFields))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customFields));
		}
		if($customFields && \is_string($customFields))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $customFields);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if($credentials)
		{
			curl_setopt($ch, CURLOPT_USERPWD, $credentials);
		}

		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if($http_code === 204)
		{
			// cancel does not return a response only the status code 204
			return TRUE;
		}

		if(empty($result))
		{
			throw new \Exception('Error: No response.');
		}


		$response = json_decode($result);
		if(!$response->id && !$response->access_token)
		{
			$message = $response->message ?: $response->error_description;
			throw new \Exception($message);
		}
		return $response;
	}

	/**
	 * get return url
	 *
	 * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
	 * @return string
	 */
	protected function getReturnUrl(Item $orderItem):string
	{
		$objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		/** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
		$uriBuilder = $objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class);
		$uri = $uriBuilder
			->reset()
			->setCreateAbsoluteUri(TRUE)
			->setTargetPageUid($this->subscriptionSettings['subscription']['pid'])
			->uriFor('success', ['orderItem' => $orderItem->getUid()], 'Subscription', 'PaypalSubscription', 'Pi1');

		return $uri;
	}










	/**
	 * not working
	 * @param string $product
	 */
	public function deleteCatalogProduct(string $product)
	{
		$url = $this->getUrl(self::SEGMENT_PRODUCTS);
		$url .= '/' . $product;
		debug($url);
		$header = [
			'Content-Type: application/json',
			'Authorization: ' . $this->tokenType . ' ' . $this->accessToken,
		];

		$customFields = [
			[
				'op' => 'remove',
				'path' => '/0/path', // could not find right path name. also no information in the documentation how to delete a product

			],
		];

		$ch = curl_init();
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customFields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		debug(curl_getinfo ($ch));
		if(empty($result))
		{
			die('Error: No response.');
		}

		$json = json_decode($result);
		debug($json);
	}

}
