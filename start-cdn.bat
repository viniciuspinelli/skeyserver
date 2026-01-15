@echo off
echo ========================================
echo   Skey Server - CDN Local para Texturas
echo ========================================
echo.
echo Iniciando servidor HTTP na porta 8080...
echo Pasta: skins\src
echo.
cd /d "%~dp0skins\src"
start /b http-server -p 8080 --cors -c-1
echo.
echo Aguarde o ngrok iniciar...
timeout /t 3 /nobreak >nul
echo.
echo ========================================
echo   COPIE A URL HTTPS DO NGROK ABAIXO
echo ========================================
ngrok http 8080
