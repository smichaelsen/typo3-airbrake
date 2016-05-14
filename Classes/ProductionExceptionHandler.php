<?php
namespace Smichaelsen\Airbrake;

use Airbrake\EventHandler;
use Smichaelsen\ShortcutParams\TypoScriptFrontendController;
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
        $pluginConfiguration = $this->getTyposcriptFrontendController()->tmpl->setup['plugin.']['tx_airbrake.'];
        $apiKey = $contentObject->getContentObject()->stdWrap($pluginConfiguration['apiKey'], $pluginConfiguration['apiKey.']);
        $host = $contentObject->getContentObject()->stdWrap($pluginConfiguration['host'], $pluginConfiguration['host.']);
        $resource = $contentObject->getContentObject()->stdWrap($pluginConfiguration['resource'], $pluginConfiguration['resource.']);
        $exceptionHandler = EventHandler::start($apiKey, false,
            [
                'secure' => true,
                'host' => $host,
                'resource' => $resource,
                'environmentName' => (string)GeneralUtility::getApplicationContext(),
            ]
        );
        $exceptionHandler->onException($exception);
        return $message;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

}
