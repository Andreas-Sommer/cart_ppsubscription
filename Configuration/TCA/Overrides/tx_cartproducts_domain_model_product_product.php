<?php

defined('TYPO3_MODE') or die();

$LLL = 'LLL:EXT:paypal_subscription/Resources/Private/Language/locallang_db.xlf';

$temp_columns_cart_product = [
	'is_subscription' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.is_subscription',
		'displayCond' => 'FIELD:product_type:=:virtual',
		'onChange' => 'reload',
		'config' => [
			'type' => 'check',
			'items' => [
				'1' => [
					'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
				]
			],
			'default' => 0,
		]
	],
	'paypal_type' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_type',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'radio',
			'items' => \Belsignum\PaypalSubscription\Utility\TcaUtility::paypalTypes(),
			'default' => 'digital',
		]
	],
	'paypal_category' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_category',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'enableMultiSelectFilterTextfield' => TRUE,
			'items' => \Belsignum\PaypalSubscription\Utility\TcaUtility::paypalCategories(),
			'minitems' => 1,
			'maxitems' => 1,
		]
	],
	'paypal_sequence' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_sequence',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'inline',
			'foreign_table' => 'tx_paypalsubscription_domain_model_sequence',
			'foreign_field' => 'product',
			'foreign_sortby' => 'sorting',
			'minitems' => 1,
			'maxitems' => 9999,
			'appearance' => [
				'collapseAll' => 1,
				'levelLinksPosition' => 'top',
				'showSynchronizationLink' => 1,
				'showPossibleLocalizationRecords' => 1,
				'useSortable' => 1,
				'showAllLocalizationLink' => 1
			],
		]
	],
	'paypal_setup_failure' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_setup_failure',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'radio',
			'items' => \Belsignum\PaypalSubscription\Utility\TcaUtility::setupFailure(),
			'default' => 'cancel',
		]
	],
	'paypal_failure_threshold' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_failure_threshold',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'input',
			'eval' => 'int',
			'default' => 0,
			'range' => [
				'lower' => 0,
				'upper' => 998,
			],

		]
	],
	'paypal_product_id' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_product_id',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'none',
		]
	],
	'paypal_plan_id' => [
		'exclude' => false,
		'label' => $LLL . ':tx_cartproducts_domain_model_product_product.paypal_plan_id',
		'displayCond' => 'FIELD:is_subscription:=:1',
		'config' => [
			'type' => 'none',
		]
	],

];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_cartproducts_domain_model_product_product', $temp_columns_cart_product);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tx_cartproducts_domain_model_product_product',
	',--div--;Subscription,
	is_subscription, paypal_type, paypal_category, paypal_setup_failure, paypal_failure_threshold, paypal_sequence, paypal_product_id, paypal_plan_id'
);

/**
 * these fields are not used by subscriptions, so we hide them if is_subscription is checked
 * @var array $hideFieldsIfSubscription
 */
$hideFieldsIfSubscription = ['price', 'special_prices', 'quantity_discounts', 'min_number_in_order', 'max_number_in_order'];
foreach ($hideFieldsIfSubscription as $_ => $field)
{
	$GLOBALS['TCA']['tx_cartproducts_domain_model_product_product']['columns'][$field]['displayCond'] = 'FIELD:is_subscription:!=:1';
}

