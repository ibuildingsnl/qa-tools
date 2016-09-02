Phar
====

The QA Tools are distributed as a PHP Archive (Phar) file in order to separate the QA Tools' dependencies from
the host projects' dependencies. The compiled Phar is also able to verify its
own integrity with the provided public key.

## Types of builds

There are two types of build: a test build, and a release build. The test build is used
for system testing and is signed using an insecure, ephemeral private key. The
release build is for release to the public, and is signed using a secure private
key managed by Ibuildings.

This serves the following purposes:

 0. The test process can be completely automated on the contributor's machine
    and on Travis, the automated build server; the private key used for signing
    the test build requires no passphrase.
 0. There is no confusion as to with which private key the build is signed. This
    prevents a release of a Phar signed with the test key.

## Building the Phar

[Box][^box] is used for building the QA Tools Phar file, which is configured in
the `box.json` file. It is installed as a Phar file itself to prevent version
conflicts between Box' and QA Tools' dependencies.

To enable the writing of Phar files, disable the PHP ini setting `phar.readonly`
by setting it to `Off`.

Run `make build-test` in the project root directory to build the test build.
This creates `./build/test/qa-tools.phar` and `./build/test/qa-tools.phar.pubkey`.

## Updating Box
Box is installed during Composer's `post-install-cmd` phase.
To update Box, manually update the source and sha-sum inside `composer.json` under the `extra` parameters.

[^box]: https://box-project.github.io/box2/
[^secure-phar]: https://mwop.net/blog/2015-12-14-secure-phar-automation.html
