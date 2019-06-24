<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 11.06.2019
 * Time: 15:45
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
