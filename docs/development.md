Development
===========

To get ready for contributing to the Ibuildings QA Tools:

```shell-session
$ # Check out the repository
$ git clone git@github.com:ibuildingsnl/qa-tools-v3.git
$ # Install the Composer dependencies
$ composer install
$ # Generate an insecure signing key for signing the phars you will build
$ make generate-insecure-signing-key
$ # Install the Git hooks
$ tools/git/pre-commit-install
$ tools/git/pre-push-install
```

## Building a QA Tools distributable phar

The command below will build the QA Tools. For more specifics about building the
phar, see [Phar](phar.md).

```shell-session
$ make build
```

## Making your contribution

We appreciate you helping out! Please look at the
[Contributing guidelines](../CONTRIBUTING.md) to help get your contribution
accepted.

