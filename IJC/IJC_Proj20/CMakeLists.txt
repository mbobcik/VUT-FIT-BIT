cmake_minimum_required(VERSION 3.3)
project(IJC_Proj20)

set(CMAKE_C_FLAGS "${CMAKE_C_FLAGS} -std=c99 -pedantic -Wall -Wextra")

set(SOURCE_FILES tail.c error.c error.h)
add_executable(IJC_Proj20 ${SOURCE_FILES})