.RECIPEPREFIX +=

help:
    @echo
    @echo "\033[0;33mAvailable targets:\033[0m"
    @cat Makefile | sed 's/: /: → /' | GREP_COLORS="ms=00;32" grep --colour=always -P '^[a-z0-9].+:' | column -s ':' -t  | sed 's/^/  /'

test: phpunit-fast phpunit-slow

test-fast: phpunit-fast

phpunit-fast: phpunit-unit phpunit-integration
phpunit-slow: phpunit-functional

phpunit-unit:
    phpunit -c . --testsuite unit
phpunit-integration:
    phpunit -c . --testsuite integration
phpunit-functional:
    phpunit -c . --testsuite functional
