@echo off
echo Starting Staplr PHP Server on http://localhost:8000 ...
php -S localhost:8000 -t "%~dp0"
pause
