<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 21.05.2019
 * Time: 11:16
 */

defined('TYPO3_MODE') or die();

$iconPath = 'EXT:paypal_subscription/Resources/Public/Icons/';
$_LLL_db = 'LLL:EXT:paypal_subscription/Resources/Private/Language/locallang_db.xlf:';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Belsignum.paypal_subscription',
	'Pi1',
	'Paypal Subscription'
);

// add Paypal subscription plans
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
	'Belsignum.paypal_subscription',
	'Cart',
	'Plans',
	'',
	[
		'Backend\Subscription' => 'product, update',
	],
	[
		'access' => 'user, group',
		'icon' => $iconPath . 'paypal-icon.png',
		'labels' => $_LLL_db . 'tx_paypalsubscription.module.paypal_plans',
		'navigationComponentId' => 'typo3-pagetree',
	]
);

/**
 * Register Static Template file
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('paypal_subscription', 'Configuration/TypoScript', 'Shopping Cart - Paypal Subscription');
