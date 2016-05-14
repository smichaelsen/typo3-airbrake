<?php
namespace Smichaelsen\Airbrake;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class ProductionExceptionHandler extends \TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler
{

    /**
     * Handles exceptions thrown during rendering of content objects
     * The handler can decide whether to re-throw the exception or
     * return a nice error message for production context.
     *
     * @param \Exception $exception
     * @param AbstractContentObject $contentObject
     * @param array $contentObjectConfiguration
     * @return string
     * @throws \Exception
     */
    public function handle(\Exception $exception, AbstractContentObject $contentObject = null, $contentObjectConfiguration = array())
    {
        $message = parent::handle($exception);
        
        return $message;
    }

}
