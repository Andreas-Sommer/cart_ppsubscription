<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 11:36
 */

namespace Belsignum\PaypalSubscription\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class SubscriptionUtility
{
	protected const PAYPAL_API_SANDBOX = 'https://api.sandbox.paypal.com/v1/';
	protected const PAYPAL_API_LIVE = 'https://api.paypal.com/v1/';
	protected const PATH_SEGMENT_OAUTH = 'oauth2/token';

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


	public function __construct($settings)
	{
		$this->client = $settings['client'];
		$this->secret = $settings['secret'];
		$this->sandbox = (int) $settings['sandbox'];
		$this->requestAccessToken();
	}

	public function requestAccessToken():void
	{
		$url = $this->sandbox === 1 ? self::PAYPAL_API_SANDBOX : self::PAYPAL_API_LIVE;
		$url .= self::PATH_SEGMENT_OAUTH;
		$header = [
			'Accept' => 'application/json',
			'Accept-Language' => 'en_US'
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

}
