@echo off
REM ================================================================
REM  MusicSolutions - Arranque rapido (Windows + XAMPP)
REM  Doble clic para: encender MySQL si hace falta, levantar el
REM  servidor web y abrir el navegador en http://localhost:8000
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

echo.
echo  [..] Levantando el servidor web en http://localhost:8000
echo.

REM --- 2. Servidor PHP + navegador ---------------------------------
start "MusicSolutions PHP" /min "C:\xampp\php\php.exe" -S localhost:8000 -t public public/router.php
timeout /t 3 /nobreak >nul
start "" "http://localhost:8000"

echo.
echo  Ya puedes entrar con:
echo    Usuario:  demo@musicsolutions.test
echo    Password: password
echo.
echo  Para detener el servidor, cierra la ventana "MusicSolutions PHP".
echo  Para detener MySQL, usa el panel de XAMPP.
echo.
pause