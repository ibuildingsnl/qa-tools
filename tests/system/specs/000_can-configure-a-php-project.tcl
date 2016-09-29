test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[3\] Drupal 8" with "3"

answer "Would you like to integrate Travis in your project?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"

should_see "What ruleset would you like to use as a base?"
answer "\[1\] PSR2" with "1"

answer "Would you like to allow longer lines than the default? Warn at 120 and fail at 150." with "Y"

answer "Would you like to skip any sniffs regarding the doc blocks in tests?" with "Y"

answer "Where are the tests located for which doc block sniffs will be disabled?" with "tests/*";

answer "Would you like PHPCS to ignore some locations completely? (you may use a regex to match multiple directories)" with "Y"

answer "Which locations should be ignored?" with "behat/*"


# Allow Composer to do its thing
set timeout 5

exits_with 0
