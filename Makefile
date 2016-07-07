.RECIPEPREFIX +=

ULIMIT := $(shell command -v ulimit 2>/dev/null)

help:
    @echo
    @echo "\033[0;33mAvailable targets:\033[0m"
    @cat Makefile | sed 's/: /: → /' | GREP_COLORS="ms=00;32" grep --colour=always -P '^[a-z0-9].+:' | column -s ':' -t  | sed 's/^/  /'

build: test
ifdef ULIMIT
    # Increase open file limit
    # See https://github.com/box-project/box2/issues/80#issuecomment-76630852
    ulimit -Sn 4096 && composer build
else
    composer build
endif

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

generate-insecure-signing-key:
    @test ! -e .travis/phar-private.pem || (echo "\n  \033[0;31mA signing key already exists. Remove it if you want to generate a new one.\033[0m\n" && false)
    openssl genrsa -passout pass:insecure -des3 -out .travis/phar-private-passphrase.pem 4096 && \
        openssl rsa -passin pass:insecure -in .travis/phar-private-passphrase.pem -out .travis/phar-private.pem && \
        rm .travis/phar-private-passphrase.pem
