@echo off
echo Installing Laravel Dependencies...

REM Check if composer is installed
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Composer is not installed. Please install Composer first.
    echo Download from: https://getcomposer.org/download/
    pause
    exit /b 1
)

REM Install PHP dependencies
echo Installing PHP dependencies...
composer install --no-interaction

REM Check if npm is installed
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo npm is not installed. Please install Node.js first.
    echo Download from: https://nodejs.org/
    pause
    exit /b 1
)

REM Install Node.js dependencies
echo Installing Node.js dependencies...
npm install

echo.
echo Dependencies installed successfully!
echo.
echo Next steps:
echo 1. Copy env.example to .env
echo 2. Run: php artisan key:generate
echo 3. Create database: liquid_vending
echo 4. Run: php artisan migrate --seed
echo 5. Start server: php artisan serve
echo 6. Start frontend: npm run dev
echo.
pause
