@echo off
set msbuild=C:\Windows\Microsoft.NET\Framework64\v4.0.30319\msbuild

IF "%1"=="" GOTO all
IF "%1"=="all" GOTO all
IF "%1"=="build" GOTO build
IF "%1"=="docs" GOTO docs
IF "%1"=="install" GOTO install
IF "%1"=="test" GOTO test
IF "%1"=="clean" GOTO clean

exit /B

:all
call:build
call:docs
call:install
goto :eof

:build
%msbuild% source\Calculator\Calculator.csproj
%msbuild% source\UnitTest\UnitTestConsole.csproj
goto :eof

:docs
cd source
mkdir docs
Doxygen\doxygen.exe doxConf
cd ..
goto :eof

:install
mkdir build
move source\Calculator\bin\debug\Calculator.exe build\
move source\Calculator\bin\debug\ParserLib.dll build\
move source\UnitTest\bin\debug\UnitTestConsole.exe build\
goto :eof

:test
build\UnitTestConsole.exe
goto :eof

:clean
rmdir /s /q build
rmdir /s /q source\Calculator\bin\debug
rmdir /s /q source\ParserLib\bin\debug
rmdir /s /q source\UnitTest\bin\debug
rmdir /s /q source\docs
goto :eof
