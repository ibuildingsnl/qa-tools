test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[4\] Other PHP Project" with "4"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"

should_see "What ruleset would you like to use as a base?"
answer "\[1\] PSR2" with "1"

answer "How would you like to handle line lengths?" with "Warn when >120. Fail when >150"

answer "Would you like to skip any sniffs regarding the doc blocks in tests?" with "Y"

answer "Where are the tests located for which doc block sniffs will be disabled?" with "tests/*";

answer "Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)" with "Y"

answer "Which locations should be ignored?" with "behat/*"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "Y"

give_tasks_time_to_run

exits_with 0

############### PHPCS for "Other PHP Projects"

test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Float Flush"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[4\] Other PHP Project" with "4"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"

should_see "What ruleset would you like to use as a base?"
answer "\[1\] PSR2" with "1"

answer "How would you like to handle line lengths?" with "Warn when >120. Fail when >150"

answer "Would you like to skip any sniffs regarding the doc blocks in tests?" with "Y"

answer "Where are the tests located for which doc block sniffs will be disabled?" with "tests/*";

answer "Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)" with "Y"

answer "Which locations should be ignored?" with "behat/*"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "Y"


give_tasks_time_to_run

exits_with 0


############### PHPCS for "Symfony 3"

test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Tuple Thrust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[1\] Symfony 3" with "1"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"
should_see "What ruleset would you like to use as a base?"
answer "\[3\] Symfony" with "3"
answer "How would you like to handle line lengths?" with "Warn when >120. Fail when >150"
answer "Would you like to skip any sniffs regarding the doc blocks in tests?" with "n"
answer "Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)" with "n"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "Y"

give_tasks_time_to_run

exits_with 0

############### PHPCS for "Symfony 2"

test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Crypto Crunch"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"
should_see "What ruleset would you like to use as a base?"
answer "\[1\] PSR2" with "1"
answer "How would you like to handle line lengths?" with "Warn when >120. Fail when >150"
answer "Would you like to skip any sniffs regarding the doc blocks in tests?" with "n"
answer "Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)" with "n"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "Y"

give_tasks_time_to_run

exits_with 0

############### PHPCS for "Drupal 7"

test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Lunch Lost"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[2\] Drupal 7" with "2"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "Y"

give_tasks_time_to_run

exits_with 0

############### for "Drupal 8"s

test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Monads Mocked"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[3\] Drupal 8" with "3"

answer "Would you like to install PHPUnit for running automated tests?" with "Y"

answer "Would you like to lint PHP files?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"

answer "Would you like to check for vulnerable dependencies using SensioLabs Security Checker?" with "Y"

answer "Would you like to install Behat?" with "Y"

give_tasks_time_to_run

exits_with 0

###############
