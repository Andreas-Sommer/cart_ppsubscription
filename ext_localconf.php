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

$dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
$dispatcher->connect(
	\Extcode\Cart\Utility\PaymentUtility::class,
	'handlePayment',
	\Belsignum\PaypalSubscription\Utility\PaymentUtility::class,
	'handlePayment'
);

$dispatcher->connect(
	\Extcode\Cart\Utility\OrderUtility::class,
	'addProductAdditionalData',
	\Belsignum\PaypalSubscription\Utility\OrderUtility::class,
	'addProductAdditionalData'
);


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Belsignum.paypal_subscription',
	'Pi1',
	[
		'Subscription' => 'list, cancel, success',
	],
	// non-cacheable actions
	[
		'Subscription' => 'list, cancel, success',
	]
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
