<?php
namespace Smichaelsen\Airbrake\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class PluginConfigurationService implements SingletonInterface
{
    public function getPluginConfiguration(): array
    {
        static $pluginConfiguration;
        if (!is_array($pluginConfiguration)) {
            $pluginConfiguration = self::loadPackageTypoScriptSettings();
            $stdWrapProperties = GeneralUtility::trimExplode(',', 'apiKey, host, resource, stillLogExceptionToLogfile');
            $cObj = self::getContentObjectRenderer();
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

    protected static function getContentObjectRenderer(): ContentObjectRenderer
    {
        if (isset($GLOBALS['TSFE'])) {
            return $GLOBALS['TSFE']->cObj;
        }
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObjectRenderer->start([]);
        return $contentObjectRenderer;
    }

    protected static function loadPackageTypoScriptSettings(): array
    {
        if (TYPO3_MODE === 'FE') {
            $configurationManager = GeneralUtility::makeInstance(FrontendConfigurationManager::class);
        } else {
            $configurationManager = GeneralUtility::makeInstance(BackendConfigurationManager::class);
        }
        $typoScript = $configurationManager->getTypoScriptSetup();
        if (empty($typoScript['plugin.']['tx_airbrake.'])) {
            return [];
        }
        return GeneralUtility::makeInstance(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($typoScript['plugin.']['tx_airbrake.']);
    }
}
