Upgrading from 2.x to 3.0
=========================

The third major release of QA Tools takes a whole different approach and requires you to reconfigure your project's quality assurance tools. If this is acceptable, follow these steps to remove QA Tools 2.x from your project:

 * Remove all artifacts related to QA Tools 2.x: `qa-tools.json`, `.jshintrc`, `.travis.php.ini`, `.travis.yml`, `behat.dev.yml`, `behat.yml`, `build-pre-commit.xml`, `build.xml`, `phpcs.xml`, `phpmd-pre-commit.xml`, `phpmd.xml`, `phpunit.xml`, `pre-commit`.
 * Remove `ibuildings/qa-tools` from your Composer file.
 * Install QA Tools 3 by following the [installation instructions](./README.md#Installation)
 * Reconfigure QA Tools by calling `/path/to/qa-tools.phar configure` in your project directory.
