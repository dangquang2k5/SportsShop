@echo off
REM Script to set up the reports directory on Windows

echo Setting up reports directory...
echo.

REM Create reports directory if it doesn't exist
if not exist "reports" (
    mkdir reports
    echo Created reports directory
) else (
    echo Reports directory already exists
)

REM Create necessary files
type nul > reports\.htaccess
type nul > reports\index.php
type nul > reports\.gitignore
type nul > reports\README.md

echo Created configuration files
echo.
echo Reports directory setup complete!
echo.
dir reports
pause
