<?php
namespace CoelnConcept\CcImage\DataProcessing;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * This data processor can be used for processing data for record which contain
 * relations to sys_file records (e.g. sys_file_reference records).
 *
 *
 * Example TypoScript configuration:
 *
 * 10 = CoelnConcept\CcImage\DataProcessing\FilesProcessor
 * 10 {
 *   filereferences {
 *	   data = levelmedia:-1,slide
 *   }
 *   as = myfiles
 * }
 *
 * whereas "myfiles" can further be used as a variable {myfiles} inside a Fluid template for iteration.
 */
class FilesProcessor implements DataProcessorInterface {
	
	/**
	 * Process data of a record to resolve File objects to the view
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData) {
		if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}
		
		// gather data
		/** @var FileCollector $fileCollector */
		$fileCollector = GeneralUtility::makeInstance(FileCollector::class);
		
		$filereferences = $cObj->stdWrapValue('filereferences', $processorConfiguration);
		if ($filereferences) {
			$filereferences = GeneralUtility::intExplode(',', $filereferences, true);
			$fileCollector->addFileReferences($filereferences);
		}
		
		// make sure to sort the files
		$sortingProperty = $cObj->stdWrapValue('sorting', $processorConfiguration);
		if ($sortingProperty) {
			$sortingDirection = $cObj->stdWrapValue(
				'direction',
				$processorConfiguration['sorting.'] ?? [],
				'ascending'
			);
			
			$fileCollector->sort($sortingProperty, $sortingDirection);
		}
		
		// set the files into a variable, default "files"
		$targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'filereferences');
		$processedData[$targetVariableName] = $fileCollector->getFiles();
		
		return $processedData;
	}
}
