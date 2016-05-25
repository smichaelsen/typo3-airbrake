<?php
namespace Smichaelsen\Airbrake\ExceptionHandler;

use Smichaelsen\Airbrake\Service\AirbrakeService;
use Smichaelsen\Airbrake\Service\PluginConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler;
use TYPO3\CMS\Lang\LanguageService;

class ContentObjectExceptionHandler extends ProductionExceptionHandler
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
        $pluginConfiguration = GeneralUtility::makeInstance(PluginConfigurationService::class)->getPluginConfiguration();
        $this->stillLogExceptionToLogfile = (bool)$pluginConfiguration['stillLogExceptionToLogfile'];
        // Change the default message if no file logging is enabled. There is no need to output an exception identifier to the user.
        if (!$this->stillLogExceptionToLogfile && !isset($this->configuration['errorMessage'])) {
            $this->configuration['errorMessage'] = $this->getLanguageService()->sL('LLL:EXT:airbrake/Resources/Private/Language/locallang.xlf:defaultErrorMessage');
        }
        $message = parent::handle($exception);
        GeneralUtility::makeInstance(AirbrakeService::class)->handleException($exception);
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
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return isset($GLOBALS['LANG']) ? $GLOBALS['LANG'] : GeneralUtility::makeInstance(LanguageService::class);
    }

}
