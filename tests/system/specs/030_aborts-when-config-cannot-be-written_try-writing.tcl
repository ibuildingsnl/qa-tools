test ./qa-tools configure --no-ansi -v

accept_default_for "What is the project's name?"
accept_default_for "Where would you like to store the generated files?"
accept_default_for "What type of project would you like to configure?"
accept_default_for "What type of PHP project would you like to configure?"
accept_default_for "Would you like to integrate Travis in your project?"
accept_default_for "Would you like to run automated tests with PHPUnit?"
accept_default_for "Would you like to use PHP Lint?"
accept_default_for "Would you like to use PHP Mess Detector?"
accept_default_for "Would you like to use PHP Code Sniffer?"
accept_default_for "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?"

set timeout 5

should_see "Could not write data to file \"./qa-tools.json\""

exits_with 1
