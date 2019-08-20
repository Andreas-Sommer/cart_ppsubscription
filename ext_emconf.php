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

/***************************************************************
 * Extension Manager/Repository config file for ext "paypal_subscription".
 *
 * Auto generated 07-05-2019 10:38
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
  'title' => 'Paypal Subscription',
  'description' => 'Paypal Subscription for Cart',
  'category' => 'plugin',
  'version' => '1.0.1',
  'state' => 'beta',
  'uploadfolder' => true,
  'createDirs' => '',
  'clearcacheonload' => true,
  'author' => 'Andreas Sommer',
  'author_email' => 'sommer@belsignum.com',
  'author_company' => 'belsignum',
  'constraints' =>
  [
    'depends' =>
    [
      'typo3' => '8.7.0-8.7.99',
	  'cart' => '5.4.0',
	  'cart_products' => '1.0.2'
    ],
    'conflicts' =>
    [],
    'suggests' =>
    [
    ],
  ],
];

