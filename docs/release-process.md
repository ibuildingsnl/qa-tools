Release process
===============

The QA Tools' release process is largely automated by [RMT][github-rmt]. The
configured release process, in this order:

 0. verifies that the working copy is clean;
 0. verifies that the software is in working order by running all tests;
 0. keeps a change log;
 0. creates a new tag according to [semver][semver] rules;
 0. and published the tag to the Git tracked repository `origin`.

If any of the verification steps fail, the release process is aborted. These
verification steps are there for a very good reason and *may never* be skipped.

The release build is signed using a separate private key and is managed by
Ibuildings. This key can be found in Ibuildings' LastPass and is named "QA Tools
private key for releases". Place this key in `./signing-key-release.pem`.

Then, to run the release process, execute the following in a terminal:

```sh-session
$ make release
```

## Distributing the release build

After the new tag has been published, the [Phar](phar.md) built during the
publishing process must be uploaded to the [Releases][github-qa-releases] page
on GitHub.

 0. Click *Draft a new release*.
 0. Select the tag you just published.
 0. Use the tag as release title.
 0. If it's an unstable version, indicated by a stability label like `#.#.#-beta`, tick the pre-release checkbox. Ticking this checkbox does not have any functional consequences for the self-updating process, but functions primarily as documentation.
 0. Copy this release's documented changes (see the change log) into the
    description field.
 0. Attach the Phar and its public key to the release. You find these in the `./build/release/`  directory.
 0. Publish the release.

[github-rmt]: https://github.com/liip/RMT
[semver]: http://semver.org/
[path]: https://en.wikipedia.org/wiki/PATH_(variable)
[github-qa-releases]: https://github.com/ibuildingsnl/qa-tools/releases
