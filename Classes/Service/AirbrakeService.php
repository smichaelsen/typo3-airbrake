<?php
namespace Smichaelsen\Airbrake\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AirbrakeService implements SingletonInterface
{
    public function handleException(\Exception $exception)
    {
        $pluginConfiguration = GeneralUtility::makeInstance(PluginConfigurationService::class)->getPluginConfiguration();
        $notifier = new \Airbrake\Notifier(
            [
                'projectId' => $pluginConfiguration['projectId'],
                'projectKey' => $pluginConfiguration['projectKey'],
                'host' => $pluginConfiguration['host'],
                'environment' => (string)GeneralUtility::getApplicationContext(),
            ]
        );
        \Airbrake\Instance::set($notifier);
        \Airbrake\Instance::notify($exception);
    }
}
