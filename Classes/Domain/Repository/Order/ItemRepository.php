<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 11.06.2019
 * Time: 15:45
 */

namespace Belsignum\PaypalSubscription\Domain\Repository\Order;

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
}
