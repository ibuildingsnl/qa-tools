test ./qa-tools configure --no-ansi

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to integrate Travis in your project?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

assert_success

###############

test ./qa-tools configure --no-ansi

accept_default_for "What is the project's name?"
accept_default_for "Where would you like to store the generated files?"
accept_default_for "What type of project would you like to configure?"
accept_default_for "What type of PHP project would you like to configure?"
accept_default_for "Would you like to integrate Travis in your project?"
accept_default_for "Would you like to use PHP Mess Detector?"

assert_success
