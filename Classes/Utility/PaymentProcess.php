<?php
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 11.06.2019
 * Time: 17:29
 */

namespace Belsignum\PaypalSubscription\Utility;

use Extcode\Cart\Domain\Model\Order\Transaction;
use Extcode\Cart\Domain\Repository\Order\PaymentRepository;
use Extcode\Cart\Domain\Repository\Order\TransactionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Belsignum\PaypalSubscription\Domain\Repository\Order;
use Belsignum\PaypalSubscription\Domain\Model\Order\Item;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;


class PaymentProcess
{
	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var Order\ItemRepository
	 */
	protected $orderItemRepository;

	/**
	 * @var TypoScriptService
	 */
	protected $typoScriptService;

	/**
	 * @var PersistenceManager
	 */
	protected $persistenceManager;

	/**
	 * @var TransactionRepository
	 */
	protected $transactionRepository;

	/**
	 * @var PaymentRepository
	 */
	protected $paymentRepository;

	/**
	 * @var Dispatcher
	 */
	protected $signalSlotDispatcher;

	public function __construct()
	{
		$this->objectManager = GeneralUtility::makeInstance(
			ObjectManager::class
		);

		$this->orderItemRepository = $this->objectManager->get(
			Order\ItemRepository::class
		);

		$this->typoScriptService = $this->objectManager->get(
			TypoScriptService::class
		);

		$this->persistenceManager = $this->objectManager->get(
			PersistenceManager::class
		);

		$this->transactionRepository = $this->objectManager->get(
			TransactionRepository::class
		);

		$this->paymentRepository = $this->objectManager->get(
			PaymentRepository::class
		);

		$this->signalSlotDispatcher = $this->objectManager->get(
			Dispatcher::class
		);

		$this->getTypoScript();
	}

	/**
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 * @param \Psr\Http\Message\ResponseInterface      $response
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public function process(
		ServerRequestInterface $request,
		ResponseInterface $response
	):ResponseInterface
	{
		switch ($request->getMethod()) {
			case 'POST':
				$rawPostData = file_get_contents('php://input');
				$curlRequest = json_decode($rawPostData);
				$this->writeLog($curlRequest);
				$this->processPostRequest($curlRequest);
				break;
			case 'GET':
				// for debug or manual purpose only
				$rawPostData = file_get_contents($_SERVER['DOCUMENT_ROOT'] . 'subscription.txt');
				$curlRequest = json_decode($rawPostData);
				$this->processPostRequest($curlRequest);
				break;
			default:
				$response->withStatus(405, 'Method not allowed');
		}
		return $response;
	}

	/**
	 * Process the post request
	 * @param mixed $curlRequest
	 * @throws \Exception
	 * @return void
	 */
	public function processPostRequest($curlRequest):void
	{
		if($curlRequest && $curlRequest->event_type)
		{
			switch ($curlRequest->event_type)
			{
				case 'BILLING.SUBSCRIPTION.CREATED':
					$this->setBillingSubscriptionCreated($curlRequest);
					break;
				case 'BILLING.SUBSCRIPTION.CANCELLED':
					$this->setBillingSubscriptionCanceled($curlRequest);
					break;
				case 'PAYMENT.SALE.COMPLETED':
					$this->setPaymentSaleCompleted($curlRequest);
					break;
				default:
					// unhandled event type

					break;
			}
		}
	}

	/**
	 * Set Payment status as pending and store paypal data to note
	 *
	 * @param $curlRequest
	 * @throws \Exception
	 * @return void
	 */
	protected function setBillingSubscriptionCreated($curlRequest):void
	{
		try {
			/** @var Item $orderItem */
			$orderItem = $this->orderItemRepository->findOneByPaypalSubscriptionId(
				$curlRequest->resource->id
			);

			$additionalData = $orderItem->getAdditional();
			$additionalData[] = $curlRequest;
			$orderItem->setAdditional($additionalData);

			$payment = $orderItem->getPayment();
			$payment->setStatus('pending');

			$this->paymentRepository->update($payment);
			$this->orderItemRepository->update($orderItem);
			$this->persistenceManager->persistAll();
		}
		catch (\Exception $exception)
		{
			throw $exception;
		}
	}

	/**
	 * Set Payment status as canceled and store paypal data to note
	 *
	 * @param $curlRequest
	 * @throws \Exception
	 * @return void
	 */
	protected function setBillingSubscriptionCanceled($curlRequest):void
	{
		try {
			/** @var Item $orderItem */
			$orderItem = $this->orderItemRepository->findOneByPaypalSubscriptionId(
				$curlRequest->resource->id
			);

			$additionalData = $orderItem->getAdditional();
			$additionalData[] = $curlRequest;
			$orderItem->setAdditional($additionalData);

			$payment = $orderItem->getPayment();
			$payment->setStatus('canceled');

			$this->paymentRepository->update($payment);
			$this->orderItemRepository->update($orderItem);
			$this->persistenceManager->persistAll();
		}
		catch (\Exception $exception)
		{
			throw $exception;
		}
	}

	/**
	 * Set payment status paid and adds transaction
	 *
	 * @param $curlRequest
	 * @throws \Exception
	 * @return void
	 */
	protected function setPaymentSaleCompleted($curlRequest):void
	{
		try {
			/** @var Item $orderItem */
			$orderItem = $this->orderItemRepository->findOneByPaypalSubscriptionId(
				$curlRequest->resource->billing_agreement_id
			);

			$payment = $orderItem->getPayment();

			$transaction = new Transaction();
			$transaction->setTxnId($curlRequest->id);
			$transaction->setTxnTxt(json_encode($curlRequest));
			$transaction->setStatus($curlRequest->resource->state);
			$transaction->setPid($payment->getPid());

			$this->transactionRepository->add($transaction);
			$payment->addTransaction($transaction);
			$payment->setStatus('paid');
			$this->paymentRepository->update($payment);
			$this->persistenceManager->persistAll();

			$this->signalSlotDispatcher->dispatch(
				__CLASS__,
				__FUNCTION__ . 'AfterPersisted',
				[$orderItem]
			);
		}
		catch (\Exception $exception)
		{
			throw $exception;
		}
	}

	/**
	 * initialize TypoScript environment
	 * @return void
	 */
	protected function getTypoScript():void
	{
		$pageId = (int)GeneralUtility::_GP('pageid');
		$GLOBALS['TSFE'] = GeneralUtility::makeInstance(
			\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
			$GLOBALS['TYPO3_CONF_VARS'],
			$pageId,
			0,
			true
		);
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();

		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();

		$GLOBALS['TSFE']->initUserGroups();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->sys_page = GeneralUtility::makeInstance(
			\TYPO3\CMS\Frontend\Page\PageRepository::class
		);
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		$this->conf = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cartpaypal.']
		);
		$this->cartConf = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cart.']
		);
	}

	/**
	 * store data in log file
	 *
	 * @param $data
	 * @return void
	 */
	protected function writeLog($data):void
	{
		$logDir = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT')
				   . '/uploads/tx_paypalsubscription/notifications/';
		if(!is_dir($logDir) && !mkdir($logDir) && !is_dir($logDir)) {
			print ('Not able to create log dir');
		}
		else
		{
			$logFile = $logDir . 'PaypalLog.txt';
			$fp = fopen($logFile, 'a');
			fwrite($fp, json_encode($data) . "\n");
			fclose($fp);
		}
	}
}
