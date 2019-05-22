<?php

defined('TYPO3_MODE') or die();


$temp_columns_cart_product = [
	'is_subscription' => [
		'exclude' => false,
		'label' => 'LLL:EXT:paypal_subscription/Resources/Private/Language/locallang_db.xlf:tx_cartproducts_domain_model_product_product.is_subscription',
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
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_cartproducts_domain_model_product_product', $temp_columns_cart_product);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tx_cartproducts_domain_model_product_product',
	',--div--;Subscription,
                    is_subscription'
);
