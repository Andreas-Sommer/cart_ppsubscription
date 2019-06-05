<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 11:06
 */

defined('TYPO3_MODE') or die();

$dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$dispatcher->connect(
	\Extcode\Cart\Utility\PaymentUtility::class,
	'handlePayment',
	\Belsignum\PaypalSubscription\Utility\PaymentUtility::class,
	'handlePayment'
);


/**
 * Cart Hooks
 */
if (TYPO3_MODE === 'FE') {
	/**
	 * preselect payment type
	 */
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded']['PaypalSubscription'] =
		'EXT:paypal_subscription/Classes/Hooks/CartPaypalSubscriptionHook.php:Belsignum\PaypalSubscription\Classes\Hooks\CartPaypalSubscriptionHook->showCartActionAfterCartWasLoaded';
}



if (TYPO3_MODE === 'FE') {
	$TYPO3_CONF_VARS['FE']['eID_include']['paypal-subscription-api'] = \Belsignum\PaypalSubscription\Utility\PaymentProcess::class . '::process';
}
