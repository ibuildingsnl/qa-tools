test ./qa-tools configure --no-ansi -v

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[0\] Symfony 2" with "0"

answer "Would you like to integrate Travis in your project?" with "Y"

answer "Would you like to use PHP Lint?" with "Y"

answer "Would you like to use PHP Mess Detector?" with "Y"

answer "Would you like to use PHP Code Sniffer?" with "N"

# Allow Composer to do its thing
set timeout 5

should_see "Cannot write file \"./phpmd.xml\"; is the directory writable?"
should_see "Not all prerequisites have been met, aborting..."

exits_with 1
