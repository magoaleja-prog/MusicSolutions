@echo off
setlocal EnableDelayedExpansion
REM ================================================================
REM  MusicSolutions - Arranque rapido (Windows + XAMPP)
REM  Enciende MySQL si hace falta, busca un puerto libre (80 si es
REM  posible, para entrar sin teclear puerto) y abre el navegador.
REM  Sin bloques parentizados: compatible con cualquier cmd.exe.
REM ================================================================
title MusicSolutions
cd /d "%~dp0"

echo.
echo  ==========================================
echo   MusicSolutions - Arranque
echo  ==========================================
echo.

REM --- 1. MySQL / MariaDB -----------------------------------------
set "MYSQL_BIN=C:\xampp\mysql\bin"
set "MYSQLADMIN=%MYSQL_BIN%\mysqladmin.exe"

"%MYSQLADMIN%" -u root ping >nul 2>&1
if %ERRORLEVEL% EQU 0 goto mysql_ok

echo  [..] Arrancando MySQL (MariaDB)...
start "MusicSolutions MySQL" /min "%MYSQL_BIN%\mysqld.exe" --defaults-file=C:/xampp/mysql/bin/my.ini --standalone
timeout /t 6 /nobreak >nul
"%MYSQLADMIN%" -u root ping >nul 2>&1
if %ERRORLEVEL% EQU 0 goto mysql_ok
echo  [!!] No pude iniciar MySQL. Abrelo en el panel de XAMPP y vuelve a ejecutar.
goto puerto

:mysql_ok
echo  [OK] MySQL ya esta en marcha.

REM --- 2. Puerto libre: 80 si puede, si no 8080 o 8001 ---------------
:puerto
set "PORT=80"
call :check_port 80
if %ERRORLEVEL% EQU 0 set "PORT=8080"
call :check_port 8080
if %ERRORLEVEL% EQU 0 set "PORT=8001"

set "URL=http://localhost:%PORT%"
if "%PORT%"=="80" set "URL=http://localhost"

echo.
echo  [..] Levantando el servidor web en !URL!
echo.

REM --- 3. Servidor PHP + navegador ----------------------------------
start "MusicSolutions PHP" /min "C:\xampp\php\php.exe" -S localhost:%PORT% -t public public/router.php
timeout /t 3 /nobreak >nul

curl -s -o nul -w "%%{http_code}" "http://localhost:%PORT%/" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo  [OK] El servidor responde en !URL!
) else (
    echo  [!!] No detecte respuesta en !URL!. Comprueba la ventana "MusicSolutions PHP".
)

start "" "!URL!"

echo.
echo  Listo. Entra con:
echo    Usuario:  demo@musicsolutions.test
echo    Password: password
echo.
echo  Para detener el servidor, cierra la ventana "MusicSolutions PHP".
echo  Para detener MySQL, usa el panel de XAMPP.
echo.
pause
exit /b 0

REM ------------------------------------------------------------------
REM  Subrutina: devuelve ERRORLEVEL 0 si el puerto %~1 esta ocupado.
REM ------------------------------------------------------------------
:check_port
netstat -ano | findstr /R /C:":%~1 .*LISTENING" >nul 2>&1
if %ERRORLEVEL% EQU 0 exit /b 0
exit /b 1