<?php
namespace CoelnConcept\CcImage\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Coeln Concept GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Class ArrayNextViewHelper
 *
 * @package CoelnConcept\CcImage\ViewHelpers
 */
class ArrayNextViewHelper extends AbstractViewHelper {
	
	use CompileWithContentArgumentAndRenderStatic;
	
	/**
	 * @var boolean
	 */
	protected $escapeChildren = false;
	
	/**
	 * @var boolean
	 */
	protected $escapeOutput = false;
	
	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('subject', 'array', 'Traversable subject, array or \Traversable');
		$this->registerArgument('key', 'string', 'current array key');
	}
	
	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 * @return mixed next element
	 */
	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
		
		/** @var $array \Traversable */
		$subject = $renderChildrenClosure();
		
		$array = is_object($subject)?iterator_to_array($subject, true):(array)$subject;
		$keys = array_keys($array);
		
		$pos = array_search($arguments['key']??null, $keys);
		if ($pos === false) return null;
		
		if (!array_key_exists($pos+1, $keys)) return null;
		$next = $keys[$pos+1];
		
		return $array[$next];
	}
}
