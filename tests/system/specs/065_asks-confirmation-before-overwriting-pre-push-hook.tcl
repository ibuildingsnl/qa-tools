test ./qa-tools configure:pre-push --no-ansi -v

should_see "Installed Git pre-push hook"



test ./qa-tools configure:pre-push --no-ansi -v

answer "A pre-push hook already exists in this project. Are you sure you want to overwrite it?" with "y"

should_see "Installed Git pre-push hook"



test ./qa-tools configure:pre-push --no-ansi -v

answer "A pre-push hook already exists in this project. Are you sure you want to overwrite it?" with "n"

should_see "The pre-push hook was left unchanged. You can manually add the following to your pre-push hook:"
should_see "ant prepush"
