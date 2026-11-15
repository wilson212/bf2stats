@echo off
setlocal enabledelayedexpansion
if defined ProgramFiles(x86) (
    set "REGKEY=HKEY_LOCAL_MACHINE\SOFTWARE\WOW6432Node\EA GAMES\Battlefield 2 Server"
) else (
    set "REGKEY=HKEY_LOCAL_MACHINE\SOFTWARE\EA GAMES\Battlefield 2 Server"
)

for /f "tokens=2* skip=2" %%a in ('reg query "%REGKEY%" /v "GAMEDIR" 2^>nul') do set "BF2ServerDir=%%b"
if not defined BF2ServerDir (
    echo Battlefield 2 Server directory not found in registry. Please select it manually.
    set dialog="about:<input type=file id=FILE><script>FILE.click();new ActiveXObject('Scripting.FileSystemObject').GetStandardStream(1).WriteLine(FILE.value);close();resizeTo(0,0);</script>"
    for /f "tokens=* delims=" %%p in ('mshta.exe !dialog!') do set "BF2ServerDir=%%p"
    if "%BF2ServerDir%"=="" (
        echo Dialog Canceled - aborting.
        goto exit
    )
)

if not exist "%BF2ServerDir%\bf2_w32ded.exe" (
    echo Invalid Battlefield 2 Server Folder - aborting.
    goto exit
)

if exist "%BF2ServerDir%\python - backup" (
    echo Already installed - aborting.
    goto exit
)

echo Install by copying ^(copy^) or symbolic links ^(link^)? Select copy if you are unsure.
echo Note: Installation using symbolic links requires executing this batch with admin privileges.
:modeselect
set /p mode=Mode [copy/link]: 
if [%mode%]==[copy] goto copy
if [%mode%]==[link] goto link
goto modeselect

:exit
pause
exit /b 1

:copy
move "%BF2ServerDir%\python" "%BF2ServerDir%\python - backup"
echo Successfully created backup and copied bf2 stats python scripts
goto exit

:link
move "%BF2ServerDir%\python" "%BF2ServerDir%\python - backup"
mklink /d "%BF2ServerDir%\python" "%~dp0python"
if %ERRORLEVEL%==0 (
    echo Successfully created backup and symbolic link
) else (
    move "%BF2ServerDir%\python - backup" "%BF2ServerDir%\python"
    echo Failed to create symbolic link - did you start this script with admin privileges?
)
goto exit