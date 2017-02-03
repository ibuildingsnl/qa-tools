test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Symfony Success"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[1\] Symfony 3" with "1"

answer "Would you like to integrate Travis in your project?" with "n"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to use PHP Lint?" with "y"
answer "Would you like to use PHP Mess Detector?" with "y"
answer "Would you like to use PHP Code Sniffer?" with "y"
answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "n"
answer "Would you like to install Behat?" with "n"

give_tasks_time_to_run

exits_with 0
