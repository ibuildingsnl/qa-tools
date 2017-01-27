test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Drupal Dope"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[3\] Drupal 8" with "3"

answer "Would you like to integrate Travis in your project?" with "n"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to use PHP Lint?" with "n"
answer "Would you like to use PHP Mess Detector?" with "n"
answer "Would you like to use PHP Code Sniffer?" with "n"
answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "n"
answer "Would you like to install Behat?" with "n"

give_tasks_time_to_run

exits_with 0
