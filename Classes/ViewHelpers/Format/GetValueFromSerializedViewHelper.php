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
namespace Belsignum\PaypalSubscription\ViewHelpers\Format;

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Converts the JSON encoded argument into a PHP variable.
 */
class GetValueFromSerializedViewHelper extends AbstractViewHelper
{
	use CompileWithContentArgumentAndRenderStatic;

	/**
	 * @return void
	 */
	public function initializeArguments()
	{
		$this->registerArgument('serialized', 'string', 'Serialized string to unwrap');
		$this->registerArgument('key', 'string', 'Array Key');
	}

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 * @return mixed|null
	 * @throws Exception
	 * @throws \ReflectionException
	 */
	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
	{
		$str = $arguments['serialized'];
		$key = $arguments['key'];

		if (true === empty($str)) {
			return null;
		}
		$collection = unserialize($str, [TRUE]);

		if(\is_object($collection))
		{
			$rp = new \ReflectionProperty($collection, $key);
			if($rp->isPublic())
			{
				return $collection->$key;
			}

			$fn = 'get' . ucfirst($key);
			try
			{
				return $collection->$fn();
			}
			catch (\Exception $exception)
			{
				$exception;
			}
		}

		if(\is_array($collection))
		{
			return $collection[$key];
		}
	}
}
