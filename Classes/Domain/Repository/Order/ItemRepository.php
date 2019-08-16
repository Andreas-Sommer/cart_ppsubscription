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

namespace Belsignum\PaypalSubscription\Domain\Repository\Order;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

class ItemRepository extends \Extcode\Cart\Domain\Repository\Order\ItemRepository
{
	/**
	 * @param String $subscriptionId
	 *
	 * @return object
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
	 */
	public function findOneByPaypalSubscriptionId(string $subscriptionId)
	{
		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->getQuerySettings()->setIgnoreEnableFields(true);
		$query->getQuerySettings()->setIncludeDeleted(true);
		$query->getQuerySettings()->setRespectSysLanguage(false);

		$query->matching($query->like('paypal_subscription_id', $subscriptionId));
		return $query->execute()->getFirst();
	}

	/**
	 * find all subsction orders
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
	 */
	public function findSubsctiptionOrders()
	{
		$query = $this->createQuery();
		$query->matching(
			$query->logicalNot(
				$query->like('paypal_subscription_id', '')
			)
		);
		return $query->execute();
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
	 */
	public function findSubsctiptionOrdersByUser(FrontendUser $frontendUser)
	{
		$query = $this->createQuery();
		$query->matching(
			$query->logicalAnd(
				$query->equals('fe_user', $frontendUser->getUid()),
				$query->logicalNot(
					$query->like('paypal_subscription_id', '')
				)
			)
		);
		return $query->execute();
	}
}
