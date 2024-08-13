<?php
namespace CoelnConcept\CcImage\ViewHelpers\Image;

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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class InlineSvgViewHelper
 * Returns the contents of an svg image as inline svg.
 *
 * @package CoelnConcept\CcImage\ViewHelpers\Image
 *
 * Examples
 * ========
 *
 * Default
 * -------
 *
 * ::
 *
 *    <cc:image.inlineSvg src="EXT:cc_example/Resources/Public/logo_coelnconcept.svg" />
 *
 */
class InlineSvgViewHelper extends AbstractTagBasedViewHelper
{
	/**
	 * @var string
	 */
	protected $tagName = 'svg';

	/**
	 * @var \TYPO3\CMS\Extbase\Service\ImageService
	 */
	protected $imageService;

	/**
	 * constuctor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->imageService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Service\ImageService::class);
	}

	/**
	 * Initialize arguments.
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('src', 'string', 'a path to a file, a combined FAL identifier or an uid (int). If $treatIdAsReference is set, the integer is considered the uid of the sys_file_reference record. If you already got a FAL object, consider using the $image parameter instead', false, '');
		$this->registerArgument('treatIdAsReference', 'bool', 'given src argument is a sys_file_reference record', false, false);
		$this->registerArgument('image', 'object', 'a FAL object');
	}

	/**
	 * Return svg as inline string
	 *
	 * @throws Exception
	 * @return string Rendered inline svg
	 */
	public function render() {
		$this->arguments['src'] = $this->arguments['src'] ?? '';
		$this->arguments['image'] = $this->arguments['image'] ?? null;
		
		if (($this->arguments['src'] === '' && $this->arguments['image'] === null) || ($this->arguments['src'] !== '' && $this->arguments['image'] !== null)) {
			throw new Exception('You must either specify a string src or a File object.', 1460976233);
		}
		
		try {
			$image = $this->imageService->getImage($this->arguments['src'], $this->arguments['image'], $this->arguments['treatIdAsReference']);
		} catch (ResourceDoesNotExistException $e) {
			// thrown if file does not exist
			throw new Exception($e->getMessage(), 1509741911, $e);
		} catch (\UnexpectedValueException $e) {
			// thrown if a file has been replaced with a folder
			throw new Exception($e->getMessage(), 1509741912, $e);
		} catch (\RuntimeException $e) {
			// RuntimeException thrown if a file is outside of a storage
			throw new Exception($e->getMessage(), 1509741913, $e);
		} catch (\InvalidArgumentException $e) {
			// thrown if file storage does not exist
			throw new Exception($e->getMessage(), 1509741914, $e);
		}
		
		if ($image->getMimeType() != 'image/svg+xml') {
			throw new Exception('Mimetype of image '.$image->getPublicUrl().' is not image/svg+xml but: '.$image->getMimeType(), 1593687938);
		}
		
		$svg = $image->getContents();
		
		$matches = [];
		preg_match('/(<svg.*\/svg>)/is', $svg, $matches);
		
		return trim(preg_replace('/\\>\\s+\\</', '><',$matches[0]));
	}
}
