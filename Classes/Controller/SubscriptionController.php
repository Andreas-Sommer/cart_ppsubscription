<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 12.06.2019
 * Time: 14:43
 */

namespace Belsignum\PaypalSubscription\Controller;

use Extcode\Cart\Domain\Model\Order\Item;

class SubscriptionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * Action success
	 *
	 * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
	 * @return void
	 */
	public function successAction(Item $orderItem):void
	{
		$this->view->assign('orderItem', $orderItem);
	}
}
