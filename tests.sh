#!/usr/bin/env bash

case ${1} in
    "")
        exec vendor/bin/phpunit --configuration .
        ;;
    "--coverage")
        if which phpdbg ; then
            exec phpdbg -qrr vendor/bin/phpunit --configuration . --coverage-html=build/coverage
        else
            exec exec vendor/bin/phpunit --configuration . --coverage-html=build/coverage
        fi
        ;;
    *)
        printf >&2 "Invalid flag '%s', use '--coverage' or nothing.\n" "${1}"
esac