test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Boolean Bust"
answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to integrate Travis in your project?" with "N"
answer "Would you like to install PHPUnit for running automated tests?" with "N"
answer "Would you like to use PHP Lint?" with "N"
answer "Would you like to use PHP Mess Detector?" with "N"
answer "Would you like to use PHP Code Sniffer?" with "N"
answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "N"
answer "Would you like to install Behat?" with "N"

# Allow Composer to do its thing
set timeout 5

exits_with 0
