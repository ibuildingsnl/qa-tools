spawn ./qa-tools configure --no-ansi

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[1\] Symfony 3" with "1"

answer "Would you like to integrate Travis in your project?" with "Y"

expect_eof
