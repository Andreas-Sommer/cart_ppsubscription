<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 12.06.2019
 * Time: 14:43
 */

namespace Belsignum\PaypalSubscription\Controller;

use Belsignum\PaypalSubscription\Domain\Model\Order\Item;
use Belsignum\PaypalSubscription\Domain\Repository\Order;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

class SubscriptionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * @var Order\ItemRepository
	 */
	protected $orderItemRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 */
	protected $frontendUserRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	protected $frontendUser;

	/**
	 * Action List
	 *
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
	 * @return void
	 */
	public function listAction():void
	{
		$uuid = $GLOBALS['TSFE']->fe_user->user['uid'];
		if($uuid > 0)
		{
			$this->frontendUserRepository = $this->objectManager->get(
				FrontendUserRepository::class
			);
			$this->frontendUser = $this->frontendUserRepository->findByUid($uuid);
		}

		if($this->frontendUser)
		{
			$this->orderItemRepository = $this->objectManager->get(
				Order\ItemRepository::class
			);

			$subscriptions = $this->orderItemRepository->findSubsctiptionOrdersByUser($this->frontendUser);
			$this->view->assign('subscriptions', $subscriptions);
		}
	}

	/**
	 * Action success
	 * @param \Belsignum\PaypalSubscription\Domain\Model\Order\Item
	 * @return void
	 */
	public function successAction(Item $orderItem):void
	{
		$this->view->assign('orderItem', $orderItem);
	}
}
