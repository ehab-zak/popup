<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {
        $imagefile_ext = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']??'', true);
        if (!in_array('webp', $imagefile_ext)) $imagefile_ext[] = 'webp';
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] = implode(',',$imagefile_ext);

        $mediafile_ext = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext']??'', true);
        if (!in_array('webp', $mediafile_ext)) $mediafile_ext[] = 'webp';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] = implode(',',$mediafile_ext);
    }
);
