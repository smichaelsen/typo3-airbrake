<?php
namespace Smichaelsen\Airbrake\ExceptionHandler;

use Smichaelsen\Airbrake\Service\AirbrakeService;
use TYPO3\CMS\Core\Error\ProductionExceptionHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GeneralExceptionHandler extends ProductionExceptionHandler
{
    public function __construct()
    {
        parent::__construct();
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * @param \Exception|\Throwable $exception
     * @throws \Exception
     */
    public function handleException($exception)
    {
        GeneralUtility::makeInstance(AirbrakeService::class)->handleException($exception);
        parent::handleException($exception);
    }
}
