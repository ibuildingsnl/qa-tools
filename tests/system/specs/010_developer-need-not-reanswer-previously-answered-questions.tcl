test ./qa-tools configure --no-ansi -v

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to integrate Travis in your project?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "Y"

# Allow Composer to do its thing
set timeout 5

exits_with 0

###############

test ./qa-tools configure --no-ansi -v

accept_default_for "What is the project's name?"
accept_default_for "Where would you like to store the generated files?"
accept_default_for "What type of project would you like to configure?"
accept_default_for "What type of PHP project would you like to configure?"
accept_default_for "Would you like to integrate Travis in your project?"
accept_default_for "Would you like to use PHP Mess Detector?"
accept_default_for "Would you like to use PHP Code Sniffer?"

# Allow Composer to do its thing
set timeout 5

exits_with 0
