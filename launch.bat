@echo off
cls

title RedHotSubs launcher

set APACHE_PATH=D:\Programme\xampp\apache\bin
set MYSQL_PATH=D:\Programme\xampp\mysql\bin

start /b "" %APACHE_PATH%\httpd.exe
start /b "" %MYSQL_PATH%\mysqld --defaults-file=%MYSQL_PATH%\my.ini --standalone

start "browser" "http://localhost:8000/" 

echo:
echo Running RedHotSubs...
echo Press any key to shutdown this service
echo:

start /b "" "php" asatru serve 8000

pause

taskkill /F /IM php.exe
taskkill /F /IM mysqld.exe
taskkill /F /IM httpd.exe
