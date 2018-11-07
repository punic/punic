@ECHO OFF

pushd "%~dp0"

if "%SPHINXBUILD%" == "" (
	set SPHINXBUILD=sphinx-build
)
set SOURCEDIR=src
set BUILDDIR=%~dp0out

if "%1" == "" goto help

if "%1" == "clean" goto clean

%SPHINXBUILD% >NUL 2>NUL
if errorlevel 9009 (
	echo.
	echo.The 'sphinx-build' command was not found. Make sure you have Sphinx
	echo.installed, then set the SPHINXBUILD environment variable to point
	echo.to the full path of the 'sphinx-build' executable. Alternatively you
	echo.may add the Sphinx directory to PATH.
	echo.
	echo.If you don't have Sphinx installed, grab it from
	echo.http://sphinx-doc.org/
	exit /b 1
)


"%SPHINXBUILD%" -M "%~1" %SOURCEDIR% "%BUILDDIR%" %SPHINXOPTS%
goto end

:help
"%SPHINXBUILD%" -M help %SOURCEDIR% "%BUILDDIR%" %SPHINXOPTS%
goto end

:clean
if exist "%BUILDDIR%\*" (
    rmdir /s /q "%BUILDDIR%"
)
goto end

:end
popd
