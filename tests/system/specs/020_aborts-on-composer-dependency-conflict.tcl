test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "N"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "N"

give_tasks_time_to_run

should_see "Something went wrong while performing a dry-run install:"
should_see "  Your requirements could not be resolved to an installable set of packages."
should_see "    - phpmd/phpmd "
should_see " conflicts with "

exits_with 1
