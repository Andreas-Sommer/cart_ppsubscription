<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 11:06
 */

defined('TYPO3_MODE') or die();
// configure plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Belsignum.paypal_subscription',
	'Pi1',
	[
		'Order\Subscription' => 'product, update',
	],
	// non-cacheable actions
	[
		'Order\Subscription' => 'product, update',
	]
);

if (TYPO3_MODE === 'FE') {
	$TYPO3_CONF_VARS['FE']['eID_include']['paypal-subscription-api'] = \Belsignum\PaypalSubscription\Utility\PaymentProcess::class . '::process';
}
