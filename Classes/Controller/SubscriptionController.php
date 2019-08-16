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

namespace Belsignum\PaypalSubscription\Controller;

use Belsignum\PaypalSubscription\Domain\Model\Order\Item;
use Belsignum\PaypalSubscription\Domain\Repository\Order;
use Belsignum\PaypalSubscription\Utility\SubscriptionUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
	 * @var \Belsignum\PaypalSubscription\Utility\SubscriptionUtility
	 */
	protected $subscriptionUtility;

	/**
	 * init action
	 */
	protected function initializeAction():void
	{
		$this->frontendUserRepository = $this->objectManager->get(
			FrontendUserRepository::class
		);
	}

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
	 * action cancel
	 *
	 * @param \Belsignum\PaypalSubscription\Domain\Model\Order\Item $orderItem
	 * @return void
	 * @throws \Exception
	 */
	public function cancelAction(Item $orderItem):void
	{
		$this->subscriptionUtility = $this->objectManager->get(
			SubscriptionUtility::class,
			$this->settings
		);
		$extKey = 'paypal_subscription';
		if($this->subscriptionUtility->cancelSubscription($orderItem->getPaypalSubscriptionId()))
		{
			$orderItem->getPayment()->setStatus('pending');
			$this->orderItemRepository->update($orderItem);

			$this->addFlashMessage(
				LocalizationUtility::translate('tx_paypalsubscription_subscription.message.cancel_subscription', $extKey),
				LocalizationUtility::translate('tx_paypalsubscription_subscription.success.header', $extKey),
				\TYPO3\CMS\Core\Messaging\AbstractMessage::OK
			);
			$this->redirect('list');
		}

		$this->addFlashMessage(
			LocalizationUtility::translate('tx_paypalsubscription_subscription.message.invalid_process', $extKey),
			LocalizationUtility::translate('tx_paypalsubscription_subscription.error.header', $extKey),
			\TYPO3\CMS\Core\Messaging\AbstractMessage::OK
		);
		$this->redirect('list');
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
