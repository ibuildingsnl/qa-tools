test ./qa-tools configure:pre-commit --no-ansi -v

should_see "Installed Git pre-commit hook"



test ./qa-tools configure:pre-commit --no-ansi -v

answer "A pre-commit hook already exists in this project. Are you sure you want to overwrite it?" with "y"

should_see "Installed Git pre-commit hook"



test ./qa-tools configure:pre-commit --no-ansi -v

answer "A pre-commit hook already exists in this project. Are you sure you want to overwrite it?" with "n"

should_see "The pre-commit hook was left unchanged. You can manually add the following to your pre-commit hook:"
should_see "ant precommit"
