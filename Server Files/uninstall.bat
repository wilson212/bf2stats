@echo off
setlocal enabledelayedexpansion
if defined ProgramFiles(x86) (
    set "REGKEY=HKEY_LOCAL_MACHINE\SOFTWARE\WOW6432Node\EA GAMES\Battlefield 2 Server"
) else (
    set "REGKEY=HKEY_LOCAL_MACHINE\SOFTWARE\EA GAMES\Battlefield 2 Server2"
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

if not exist "%BF2ServerDir%\python - backup" (
    echo Not installed - aborting.
    goto exit
)

rd "%BF2ServerDir%\python"
move "%BF2ServerDir%\python - backup" "%BF2ServerDir%\python"
echo Successfully removed bf2 stats python scripts and re-installed original scripts from backup
:exit
pause
exit /b 1