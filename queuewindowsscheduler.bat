@echo off
SET "LARAVEL_PATH=%~dp0"
SCHTASKS /CREATE /TN "ProjectSigmaHrmsApiQueueWorker" /TR "%LARAVEL_PATH%artisan queue:work" /SC MINUTE /MO 10 /RL HIGHEST /F
echo Task scheduled successfully!
