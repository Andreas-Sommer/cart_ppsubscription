<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 11:16
 */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Belsignum.paypal_subscription',
	'Pi1',
	'Paypal Subscription Test (remove)'
);


/**
 * Register Static Template file
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('paypal_subscription', 'Configuration/TypoScript', 'Shopping Cart - Paypal Subscription');
