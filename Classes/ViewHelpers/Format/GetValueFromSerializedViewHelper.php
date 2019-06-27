<?php
namespace Belsignum\PaypalSubscription\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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

		#$serialized = $renderChildrenClosure();
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
			else
			{
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
		}

		if(\is_array($collection))
		{
			return $collection[$key];
		}
	}
}
