@echo off
echo Starting Staplr PHP Server on http://localhost:8000 ...
C:\xampp\php\php.exe -S localhost:8000 -t "%~dp0"
pause
