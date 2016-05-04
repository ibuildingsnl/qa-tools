# qa-tools-v3
QA-Tools v3 repo - just staging for development.

# The QA-tools PHAR File
## Building the PHAR
The QA-tools are distributed as a PHP Archive (phar) file in order to separate the QA-tools' dependencies from
the host projects' dependencies.

[Box][^box] is used for building the QA-tools phar-file, which is configured in the `box.json` file.
It is included as a phar-file itself to prevent version conflicts between Box' and QA-tools' dependencies.

Run `composer build` in the project root directory to build `./dist/qa-tools.phar` and `./dist/qa-tools.phar.pubkey`.
The public key inside `./dist/qa-tools.phar.pubkey` is generated based on `.travis/phar-private.pem`,
the git-ignored key [used to sign the phar-file][^secure-phar].

## Generating the key for signing
A private key can be generated using `openssl genrsa -des3 -out phar-private.pem 4096` inside the `.travis` directory.
To strip the passphrase for automation purposes do the following:

    cp phar-private.pem phar-private.pem.passphrase-protected
    openssl rsa -in phar-private.pem -out phar-private-nopassphrase.pem
    cp phar-private-nopassphrase.pem phar-private.pem

## Updating Box
Box is installed during Composer's `post-install-cmd` phase. 
To update Box, manually update the source and sha sum inside `composer.json` under `extra`.

[^box]: https://box-project.github.io/box2/
[^secure-phar]: https://mwop.net/blog/2015-12-14-secure-phar-automation.html
