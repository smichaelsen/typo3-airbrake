<?php
namespace Smichaelsen\Airbrake;

use Airbrake\EventHandler;
use Smichaelsen\ShortcutParams\TypoScriptFrontendController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Lang\LanguageService;

class ProductionExceptionHandler extends \TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler
{

    /**
     * @var array
     */
    protected $pluginConfiguration;

    /**
     * @var boolean
     */
    protected $stillLogExceptionToLogfile;

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
        $pluginConfiguration = $this->getPluginConfiguration($contentObject->getContentObject());
        $this->stillLogExceptionToLogfile = (bool)$pluginConfiguration['stillLogExceptionToLogfile'];
        // Change the default message if no file logging is enabled. There is no need to output an exception identifier to the user.
        if (!$this->stillLogExceptionToLogfile && !isset($this->configuration['errorMessage'])) {
            $this->configuration['errorMessage'] = $this->getLanguageService()->sL('LLL:EXT:airbrake/Resources/Private/Language/locallang.xlf:defaultErrorMessage');
        }
        $message = parent::handle($exception);
        $exceptionHandler = EventHandler::start($pluginConfiguration['apiKey'], false,
            [
                'secure' => true,
                'host' => $pluginConfiguration['host'],
                'resource' => $pluginConfiguration['resource'],
                'environmentName' => (string)GeneralUtility::getApplicationContext(),
            ]
        );
        $exceptionHandler->onException($exception);
        return $message;
    }

    /**
     * @param \Exception $exception
     * @param string $errorMessage
     * @param string $code
     */
    protected function logException(\Exception $exception, $errorMessage, $code)
    {
        if ($this->stillLogExceptionToLogfile) {
            parent::logException($exception, $errorMessage, $code);
        }
    }

    /**
     * @param ContentObjectRenderer $cObj
     * @return array
     */
    protected function getPluginConfiguration(ContentObjectRenderer $cObj)
    {
        static $pluginConfiguration;
        if (!is_array($pluginConfiguration)) {
            $pluginConfiguration = $this->getTyposcriptFrontendController()->tmpl->setup['plugin.']['tx_airbrake.'];
            $stdWrapProperties = GeneralUtility::trimExplode(',', 'apiKey, host, resource, stillLogExceptionToLogfile');
            foreach ($stdWrapProperties as $stdWrapProperty) {
                if (empty($pluginConfiguration[$stdWrapProperty])) {
                    $pluginConfiguration[$stdWrapProperty] = '';
                }
                if (empty($pluginConfiguration[$stdWrapProperty . '.'])) {
                    $pluginConfiguration[$stdWrapProperty . '.'] = [];
                }
                $pluginConfiguration[$stdWrapProperty] = $cObj->stdWrap($pluginConfiguration[$stdWrapProperty], $pluginConfiguration[$stdWrapProperty . '.']);
                unset($pluginConfiguration[$stdWrapProperty . '.']);
            }
        }
        return $pluginConfiguration;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

}
