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

// add Paypal subscriptions overview
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
	'Belsignum.paypal_subscription',
	'Cart',
	'Subscriptions',
	'',
	[
		'Backend\Subscription' => 'list',
	],
	[
		'access' => 'user, group',
		'icon' => $iconPath . 'paypal-icon.png',
		'labels' => $_LLL_db . 'tx_paypalsubscription.module.paypal_subscriptions',
		'navigationComponentId' => 'typo3-pagetree',
	]
);

/**
 * Register Static Template file
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('paypal_subscription', 'Configuration/TypoScript', 'Shopping Cart - Paypal Subscription');
