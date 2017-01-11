test ./qa-tools configure:pre-commit --no-ansi -v

should_see "Installed Git pre-commit hook"



test ./qa-tools configure:pre-commit --no-ansi -v

answer "A pre-commit hook already exists in this project. Are you sure you want to overwrite it?" with "y"

should_see "Installed Git pre-commit hook"



test ./qa-tools configure:pre-commit --no-ansi -v

answer "A pre-commit hook already exists in this project. Are you sure you want to overwrite it?" with "n"

should_see "The pre-commit hook was left unchanged. You can manually add `ant precommit` to your pre-commit hook in order to run the pre-commit build before every commit."
