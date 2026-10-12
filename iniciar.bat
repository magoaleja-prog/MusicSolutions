@echo off
setlocal EnableDelayedExpansion
REM ================================================================
REM  MusicSolutions - Arranque rapido (Windows + XAMPP)
REM  Enciende MySQL si hace falta, busca un puerto libre (80 si es
REM  posible, para entrar sin teclear :8000) y abre el navegador.
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
if %errorlevel%==0 (
    echo  [OK] MySQL ya esta en marcha.
) else (
    echo  [..] Arrancando MySQL (MariaDB)...
    start "MusicSolutions MySQL" /min "%MYSQL_BIN%\mysqld.exe" --defaults-file=C:/xampp/mysql/bin/my.ini --standalone
    timeout /t 6 /nobreak >nul
    "%MYSQLADMIN%" -u root ping >nul 2>&1
    if %errorlevel%==0 (
        echo  [OK] MySQL listo.
    ) else (
        echo  [!!] No pude iniciar MySQL. Abrelo en el panel de XAMPP y vuelve a ejecutar.
    )
)

REM --- 2. Puerto: 80 si esta libre, si no 8080 o 8001 ---------------
set "PORT=80"
netstat -ano | findstr /R /C:":!PORT! .*LISTENING" >nul 2>&1
if !errorlevel!==0 (
    set "PORT=8080"
    netstat -ano | findstr /R /C:":!PORT! .*LISTENING" >nul 2>&1
    if !errorlevel!==0 (
        set "PORT=8001"
    )
)

if "%PORT%"=="80" ( set "URL=http://localhost" ) else ( set "URL=http://localhost:!PORT!" )

echo.
echo  [..] Levantando el servidor web en !URL!
echo.

REM --- 3. Servidor PHP + navegador ----------------------------------
start "MusicSolutions PHP" /min "C:\xampp\php\php.exe" -S localhost:!PORT! -t public public/router.php
timeout /t 3 /nobreak >nul
start "" "!URL!"

echo  [OK] Abierto: !URL!
echo.
echo  Ya puedes entrar con:
echo    Usuario:  demo@musicsolutions.test
echo    Password: password
echo.
echo  Para detener el servidor, cierra la ventana "MusicSolutions PHP".
echo  Para detener MySQL, usa el panel de XAMPP.
echo.
pause