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
$LLL = 'LLL:EXT:paypal_subscription/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:sapr/Resources/Private/Language/locallang_db.xlf:tx_paypalsubscription_domain_model_sequence',
        'label' => 'type',
		#'label_alt' => 'sapr_id',
		#'label_alt_force' => true,
		'hideTable' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
		'sortby' => 'sorting',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'type, interval_unit, interval_count, total_cycles, price',
        'iconfile' => 'EXT:sapr/Resources/Public/Icons/tx_paypalsubscription_domain_model_sequence.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, type, interval_unit, interval_count, total_cycles, price',
    ],
    'types' => [
        '1' => ['showitem' => 'type, interval_unit, interval_count, total_cycles, price, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],

		'type' => [
			'exclude' => false,
			'label' => $LLL . ':tx_paypalsubscription_domain_model_sequence.type',
			'onChange' => 'reload',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.type.tenure.regular', 'regular'],
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.type.tenure.trial', 'trial'],
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.type.payment_preferences.setup_fee', 'setup_fee'],

				],
				'minitems' => 1,
				'maxitems' => 1,
			]
		],
		'interval_unit' => [
			'exclude' => false,
			'label' => $LLL . ':tx_paypalsubscription_domain_model_sequence.interval_unit',
			'displayCond' => 'FIELD:type:!=:setup_fee',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.interval_unit.day', 'day'],
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.interval_unit.week', 'week'],
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.interval_unit.semi_month', 'semi_month'],
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.interval_unit.month', 'month'],
					[$LLL . ':tx_paypalsubscription_domain_model_sequence.interval_unit.year', 'year'],
				],
				'minitems' => 1,
				'maxitems' => 1,
			]
		],
		'interval_count' => [
			'exclude' => false,
			'label' => $LLL . ':tx_paypalsubscription_domain_model_sequence.interval_count',
			'displayCond' => 'FIELD:type:!=:setup_fee',
			'config' => [
				'type' => 'input',
				'default' => 1,
				'range' => [
					'lower' => 1,
					'upper' => 365,
				],
			]
		],
		'total_cycles' => [
			'exclude' => false,
			'label' => $LLL . ':tx_paypalsubscription_domain_model_sequence.total_cycles',
			'displayCond' => 'FIELD:type:!=:setup_fee',
			'config' => [
				'type' => 'input',
				'default' => 1,
				'range' => [
					'lower' => 1,
					'upper' => 998,
				],
			]
		],
		'price' => [
			'exclude' => 1,
			'label' => $LLL . ':tx_paypalsubscription_domain_model_sequence.price',
			'displayCond' => 'FIELD:type:!=:trial',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'required,double2',
				'default' => '0.00',
			]
		],

		'product' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
    ],
];
