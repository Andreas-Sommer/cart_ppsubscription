<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:paypal_subscription/Resources/Private/Language/locallang_db.xlf';

$temp_columns_order_item = [
	'paypal_subscription_id' => [
		'exclude' => 0,
		'label' => $_LLL . ':tx_cart_domain_model_order_item.paypal_subscription_id',
		'config' => [
			'type' => 'input',
			'readOnly' => 1,
			'size' => 30,
			'eval' => 'trim'
		],
	],
];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_cart_domain_model_order_item', $temp_columns_order_item);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tx_cart_domain_model_order_item',
	',paypal_subscription_id'
);
