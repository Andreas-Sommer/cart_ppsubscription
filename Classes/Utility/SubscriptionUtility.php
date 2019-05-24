<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 11:36
 */

namespace Belsignum\PaypalSubscription\Utility;

use Belsignum\PaypalSubscription\Domain\Model\Product\Product;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SubscriptionUtility
{
	protected const PAYPAL_API_SANDBOX = 'https://api.sandbox.paypal.com/v1/';
	protected const PAYPAL_API_LIVE = 'https://api.paypal.com/v1/';
	protected const SEGMENT_OAUTH = 'oauth2/token';
	protected const SEGMENT_CATALOGS = 'catalogs/products';
	protected const SEGMENT_PLANS = 'billing/plans';

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
		$postFields = 'grant_type=client_credentials';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERPWD, $credentials);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

		$result = curl_exec($ch);
		if(empty($result))
		{
			die('Error: No response.');
		}

		$json = json_decode($result);

		$this->scope = GeneralUtility::trimExplode(' ', $json->scope);
		$this->accessToken = $json->access_token;
		$this->tokenType = $json->token_type;
		$this->appId = $json->app_id;
		$this->expiresIn = $json->expires_in;
		$this->nonce = $json->nonce;
		$this->tstamp = time();

		curl_close($ch);
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

		$url = $this->getUrl(self::SEGMENT_CATALOGS, $customFields);

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
	 * create a paypal product
	 *
	 * @param \Belsignum\PaypalSubscription\Domain\Model\Product\Product $product
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function createCatalogProduct(Product $product)
	{
		$url = $this->getUrl(self::SEGMENT_CATALOGS);

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

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customFields));
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

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customFields));
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




}
