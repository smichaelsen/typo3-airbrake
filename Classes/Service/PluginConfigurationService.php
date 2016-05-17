<?php
namespace Smichaelsen\Airbrake\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class PluginConfigurationService implements SingletonInterface
{

    /**
     * @return array
     */
    public function getPluginConfiguration()
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
                $pluginConfiguration[$stdWrapProperty] = GeneralUtility::makeInstance(ContentObjectRenderer::class)->stdWrap($pluginConfiguration[$stdWrapProperty], $pluginConfiguration[$stdWrapProperty . '.']);
                unset($pluginConfiguration[$stdWrapProperty . '.']);
            }
        }
        return $pluginConfiguration;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

}
