<?php
namespace Smichaelsen\Airbrake\Service;

use Airbrake\EventHandler;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AirbrakeService implements SingletonInterface
{

    /**
     * @param \Exception $exception
     */
    public function handleException(\Exception $exception)
    {
        $pluginConfiguration = GeneralUtility::makeInstance(PluginConfigurationService::class)->getPluginConfiguration();
        $exceptionHandler = EventHandler::start($pluginConfiguration['apiKey'], false,
            [
                'secure' => true,
                'host' => $pluginConfiguration['host'],
                'resource' => $pluginConfiguration['resource'],
                'environmentName' => (string)GeneralUtility::getApplicationContext(),
            ]
        );
        $exceptionHandler->onException($exception);
    }

    

    

}
