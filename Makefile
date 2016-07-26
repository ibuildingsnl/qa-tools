.RECIPEPREFIX +=

ULIMIT := $(shell command -v ulimit 2>/dev/null)

help:
    @echo
    @echo "\033[0;33mAvailable targets:\033[0m"
    @cat Makefile | sed 's/: /: → /' | GREP_COLORS="ms=00;32" grep --colour=always -P '^[a-z0-9].+:' | column -s ':' -t  | sed 's/^/  /'

clean:
    test ! -e dist/qa-tools.phar || rm dist/qa-tools.phar

build: dist/qa-tools.phar
dist/qa-tools.phar:
    composer install --no-dev
ifdef ULIMIT
    # Increase open file limit
    # See https://github.com/box-project/box2/issues/80#issuecomment-76630852
    (ulimit -Sn 4096 && composer build && composer install --dev) || (ret=$$?; composer install --dev && exit $$ret)
else
    (composer build && composer install --dev) || (ret=$$?; composer install --dev && exit $$ret)
endif

test: test-unit test-integration test-acceptance code-style clean build test-system test-security
test-fast: test-unit test-integration test-acceptance code-style


test-unit: phpunit-unit
test-integration: phpunit-integration
test-acceptance: behat-acceptance
test-system: phpunit-system
test-security: verify-build-is-signed check-security-advisories
code-style: phpcs


phpunit-unit:
    vendor/bin/phpunit -c . --testsuite unit
phpunit-integration:
    vendor/bin/phpunit -c . --testsuite integration
phpunit-system:
    vendor/bin/phpunit -c . --testsuite system
behat-acceptance:
    vendor/bin/behat --append-snippets
phpcs:
    # Blank line is needed to provide STDIN input to phpcs when phpcs is called from the Git pre-push hook context
    # See https://github.com/squizlabs/PHP_CodeSniffer/issues/993
    echo '' | vendor/bin/phpcs --standard=phpcs.xml --extensions=php --report=full src


verify-build-is-signed: build
    bin/box verify dist/qa-tools.phar

check-security-advisories:
    vendor/bin/security-checker security:check


generate-insecure-signing-key:
    mkdir -p .travis
    @test ! -e .travis/phar-private.pem || (echo "\n  \033[0;31mA signing key already exists. Remove it if you want to generate a new one.\033[0m\n" && false)
    openssl genrsa -passout pass:insecure -des3 -out .travis/phar-private-passphrase.pem 4096 && \
        openssl rsa -passin pass:insecure -in .travis/phar-private-passphrase.pem -out .travis/phar-private.pem && \
        rm .travis/phar-private-passphrase.pem
