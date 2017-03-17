help:
	@echo
	@echo "\033[0;33mAvailable targets:\033[0m"
	@cat Makefile | sed 's/: /: → /' | GREP_COLORS="ms=00;32" grep --colour=always -E '^[a-z0-9].+:' | column -s ':' -t  | sed 's/^/  /'

release: clean
	@test -e signing-key-release.pem || (echo "\033[31mPlease install the release build signing key from LastPass ("QA Tools private key for releases") to \033[33m./signing-key-release.pem\033[31m.\033[0m" && exit 1)
	@echo "\033[32mVerifying correct release key is installed...\033[0m"
	@cat signing-key-release.pem | tr -d " \t\n\r" | php -r 'exit((int) (sha1_file("php://stdin") !== "addecb2dba1fe389d19046eb21a933e5c0b527cb"));' \
		|| (echo "\033[31mThe file \033[33m./signing-key-release.pem\033[31m contains an unexpected private key. Please verify you installed the correct release build signing key from LastPass ("QA Tools private key for releases").\033[0m" && exit 1)
	@echo "\033[32mReleasing new version...\033[0m"
	@vendor/bin/RMT release
	@echo "\n\033[32mBuilding release build...\033[0m\n"
	@make build-release
	@echo "\n\n  \033[32mA release build is available in \033[33mbuild/release/\033[32m.\033[0m"
	@echo "\n  \033[32mPlease follow the following instructions to distribute the release build:\033[0m"
	@echo "\n  \033[33m  https://ibuildingsnl.github.io/qa-tools-v3/docs/release-process.html#distributing-the-release-build\033[0m\n"

clean:
	@rm -f box.json \
		build/test/qa-tools.phar build/test/qa-tools.phar.pubkey build/release/qa-tools.phar

build-release: build/release/qa-tools.phar
build/release/qa-tools.phar:
	@cp box.release.json box.json
	@tools/build-phar.php
	@rm box.json

build-test: build/test/qa-tools.phar
build/test/qa-tools.phar: signing-key-test.pem
	@cp box.test.json box.json
	@tools/build-phar.php
	@rm box.json

test: test-unit test-integration test-system-dev code-metrics clean build-test test-system-phar test-security verify-build
test-fast: test-unit test-integration test-system-dev code-metrics verify-readme-installer-hash

coverage:
	vendor/bin/phpunit --coverage-text --bootstrap tests/bootstrap.php --exclude-group system

test-unit: phpunit-unit
test-integration: phpunit-integration
test-system-dev: phpunit-system-dev
test-system-phar: phpunit-system-phar
test-security: verify-test-build-is-signed check-security-advisories verify-readme-installer-hash
code-metrics: phplint phpcs phpmd
verify-build: test-no-absolute-paths-in-container


phpunit-unit:
	vendor/bin/phpunit tests/unit --bootstrap tests/bootstrap.php
phpunit-integration:
	vendor/bin/phpunit tests/integration --bootstrap tests/bootstrap.php
phpunit-system-dev:
	vendor/bin/phpunit tests/system --bootstrap tests/bootstrap.php
phpunit-system-phar:
	QA_TOOLS_BIN=phar vendor/bin/phpunit tests/system --bootstrap tests/bootstrap.php
phplint:
	vendor/bin/parallel-lint --exclude vendor -e php .
phpcs:
	# Blank line is needed to provide STDIN input to phpcs when phpcs is called from the Git pre-push hook context
	# See https://github.com/squizlabs/PHP_CodeSniffer/issues/993
	echo '' | vendor/bin/phpcs --runtime-set ignore_warnings_on_exit 1 --report=full --standard=ruleset.xml --extensions=php/php src
phpmd:
	vendor/bin/phpmd src text phpmd.xml --suffixes php


verify-test-build-is-signed: build-test
	bin/box verify build/test/qa-tools.phar

check-security-advisories:
	vendor/bin/security-checker security:check

test-no-absolute-paths-in-container:
	@! grep -q $(CURDIR) var/cache/container.php \
		|| (echo "\n\033[0;31mThe compiled container contains an absolute path to the project directory. This will cause QA Tools to fail on other machines, where the absolute path will most likely not be present.\033[0m\n" \
			&& echo "\033[0;33mvar/cache/container.php:\033[0m\n" \
			&& grep $(CURDIR) var/cache/container.php)

verify-readme-installer-hash:
	tests/security/verify-readme-installer-hash

list-phpunit-test-groups:
	@grep -horE '@group .+' tests | sort | uniq


signing-key-test.pem:
	@test ! -e signing-key-test.pem || (echo "\n  \033[0;31mA signing key already exists. Remove it if you want to generate a new one.\033[0m\n" && false)
	openssl genrsa -passout pass:insecure -des3 -out signing-key-test-with-password.pem 4096 && \
		openssl rsa -passin pass:insecure -in signing-key-test-with-password.pem -out signing-key-test.pem && \
		rm signing-key-test-with-password.pem
