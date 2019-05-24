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
	'Paypal Subscription Test (remove)'
);


/**
 * Register Static Template file
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('paypal_subscription', 'Configuration/TypoScript', 'Shopping Cart - Paypal Subscription');
