Phar
====

The QA Tools are distributed as a PHP Archive (phar) file in order to separate the QA Tools' dependencies from
the host projects' dependencies.

## Building the phar

[Box][^box] is used for building the QA Tools phar-file, which is configured in the `box.json` file.
It is installed as a phar-file itself to prevent version conflicts between Box' and QA Tools' dependencies.

Run `make build` in the project root directory to build `./dist/qa-tools.phar` and `./dist/qa-tools.phar.pubkey`.
The public key inside `./dist/qa-tools.phar.pubkey` is generated based on `.travis/phar-private.pem`,
the git-ignored key [used to sign the phar-file][^secure-phar].

## Generating the key for signing

For development, an insecure, ephemeral private key can be generated using `make generate-insecure-private-key`.
For distribution, a private key will be managed by Ibuildings. 

## Updating Box
Box is installed during Composer's `post-install-cmd` phase. 
To update Box, manually update the source and sha-sum inside `composer.json` under the `extra` parameters.

[^box]: https://box-project.github.io/box2/
[^secure-phar]: https://mwop.net/blog/2015-12-14-secure-phar-automation.html
