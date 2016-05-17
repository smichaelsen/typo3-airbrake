# TYPO3 Extension: Airbrake

Logs PHP Exceptions to <a href="https://airbrake.io/">airbrake.io</a> or alternative services that are API compliant to airbrake.io.
At the moment only exceptions in ContentElement are handled. Other frontendexceptions and all backend exceptions are not supported (handled) yet.

## Quick Setup

After installing the extension, include its Static TypoScript Template and then configure your airbrake.io API key in the TS Constant Editor.

## TypoScript Reference

These options are available in `plugin.tx_airbrake`. All of them are also configurable via the constant editor. All of them have `stdWrap` enabled.

| option | default | description |
| ------ | ------- | ----------- |
| projectId | *(empty)* | Project ID for airbrake |
| projectKey | *(empty)* | Project Key for airbrake |
| host | `api.airbrake.io` | URL of your airbrake host. |
| stillLogExceptionToLogfile | `false` | TYPO3 usually logs production exceptions to typo3temp/logs/ and airbrake disables this behaviour. If this is set to true, TYPO3 will keep logging to the file. |

## Other TypoScript

These settings are not introduced by `EXT:airbrake` but can be useful for you:

| option | description |
| ------ | ----------- |
| config.contentObjectExceptionHandler | This is set to `Smichaelsen\Airbrake\ExceptionHandler\ContentObjectExceptionHandler` by `EXT:airbrake` to register it as exception handler for content objects. Unsetting it will restore TYPO3's default behaviour. |
| config.contentObjectExceptionHandler.errorMessage | This is TYPO3's option to change the default message ("Oops, an error occurred! Code: SOMEEXCEPTIONIDENTIFIER"). Also note that `EXT:airbrake` changes the TYPO3 default message to a localized message if you set nothing here. |
