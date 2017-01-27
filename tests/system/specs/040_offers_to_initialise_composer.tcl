test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[4\] Other PHP Project" with "4"

answer "Would you like to integrate Travis in your project?" with "n"
answer "Would you like to install PHPUnit for running automated tests?" with "n"
answer "Would you like to use PHP Lint?" with "n"
answer "Would you like to use PHP Mess Detector?" with "Y"
answer "Would you like to use PHP Code Sniffer?" with "n"
answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"
answer "Would you like to install Behat?" with "N"

answer "There is no Composer project initialised in this directory. Initialise one?" with "Y"
answer "Package name (<vendor>/<name>)" with "qa-tools/test-package"
accept_default_for "Description \[\]:"
answer "Author \[" with "n"
accept_default_for "Minimum Stability \[\]:"
accept_default_for "Package Type"
accept_default_for "License \[\]:"
answer "Would you like to define your dependencies (require) interactively" with "no"
answer "Would you like to define your dev dependencies (require-dev) interactively" with "no"
accept_default_for "Do you confirm generation"

# Allow tasks to run
set timeout 10

exits_with 0
