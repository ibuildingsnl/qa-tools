test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "N"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "N"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "N"

answer "Would you like to install Behat?" with "Y"

give_tasks_time_to_run

exits_with 0

###############

test ./qa-tools configure --no-ansi -v

accept_default_for "What is the project's name?"
accept_default_for "Where would you like to store the generated files?"
accept_default_for "What type of project would you like to configure?"
accept_default_for "Would you like to install PHPUnit for running automated tests?"
accept_default_for "Would you like to lint PHP files?"
accept_default_for "Would you like to use PHP Mess Detector?"
accept_default_for "Would you like to use PHP Code Sniffer?"
accept_default_for "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?"
accept_default_for "Would you like to install Behat?"

give_tasks_time_to_run

exits_with 0
