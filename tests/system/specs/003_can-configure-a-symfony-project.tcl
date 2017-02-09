test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Symfony Success"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[1\] Symfony 3" with "1"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "y"
answer "Would you like to use PHP Mess Detector?" with "y"

answer "Would you like to use PHP Code Sniffer?" with "y"
should_see "What ruleset would you like to use as a base?"
answer "\[3\] Symfony" with "3"
answer "How would you like to handle line lengths?" with "Warn when >120. Fail when >150"
answer "Would you like to skip any sniffs regarding the doc blocks in tests?" with "n"
answer "Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)" with "n"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "n"
answer "Would you like to install Behat?" with "n"

give_tasks_time_to_run

exits_with 0
