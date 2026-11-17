@echo off
title Aktifkan Domain bkprima.test
echo ========================================================
echo   MENGHUBUNGKAN DOMAIN bkprima.test KE LARAGON
echo ========================================================
echo.

:: Check for administrative privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Meminta izin Administrator...
    powershell -Command "Start-Process cmd -ArgumentList '/c \"\"%~f0\"\"' -Verb RunAs"
    exit /b
)

:: Add entry if not already present
findstr /i "bkprima.test" "%windir%\system32\drivers\etc\hosts" >nul
if %errorlevel% neq 0 (
    echo. >> "%windir%\system32\drivers\etc\hosts"
    echo 127.0.0.1      bkprima.test         #laragon magic! >> "%windir%\system32\drivers\etc\hosts"
    echo [SUKSES] Domain bkprima.test berhasil ditambahkan ke file hosts!
) else (
    echo [INFO] Domain bkprima.test sudah terdaftar di file hosts.
)

:: Flush DNS
ipconfig /flushdns >nul
echo [SUKSES] DNS Cache telah di-refresh!
echo.
echo ========================================================
echo   SELESAI! Silakan refresh browser http://bkprima.test
echo ========================================================
echo.
pause

