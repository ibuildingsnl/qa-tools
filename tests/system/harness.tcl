# Wraps the program-under-test inside a dash shell. For some reason, PHP exit
# codes are not read properly by expect, always returning 0. By wrapping the
# PHP script in a shell, the exit code propagates properly. This phenomenon is
# yet to be explained
#
# The expectation time-out is always reset to 2 seconds when calling test.
#
# Arguments:
#   args (...string) The command string for the dash shell to execute
proc test { args } {
    global spawn_id
    spawn sh -c "$args"

    set timeout 2
}

# Writes a message to screen informing the contributor the expected string did
# not appear in time. Afterwards, the script exits with status code 1.
#
# Arguments:
#   expected (string) The string that was expected to appear on screen.
proc timed_out { expected } {
    puts "Expected string \"$expected\" did not appear in time. It is likely another question is pending. Aborting..."
    exit 1
}

# Writes a message to screen informing the contributor that the program exited
# early. Afterwards, the script exits with status code 1.
#
# Arguments:
#   expected (string) The string that was expected to appear on screen.
proc early_eof { expected } {
    puts "Command terminated early while waiting for \"$expected\"."
    exit 1
}

# Asserts that the given string is expected to appear on screen. If it does not
# within the configured time-out, the script terminates with a failure.
#
# Arguments:
#   expected (string) The string that is expected to appear on screen.
proc should_see { expected } {
    expect {
        $expected {}
        timeout { timed_out $expected }
        eof { early_eof $expected }
    }
}

# Asserts that the given string is expected to appear on screen. If it appears,
# the given string is sent to the program. If it does not appear within the
# configured time-out, the script terminates with a failure.
# The 'with' argument is merely there for syntactic sugar.
#
# Example usage:
#   answer "What's in a name?" with "Unicode characters!"
# Arguments:
#   expected (string) The string that is expected to appear on screen.
#   answer (string) The string that is sent to the program in return.
proc answer { expected with answer } {
    expect {
        -exact $expected { send "$answer\n" }
        timeout { timed_out $expected }
        eof { early_eof $expected }
    }
}

# Asserts that the given string is expected to appear on screen. If it appears,
# an empty string is sent to the QA Tools that should accept the default answer.
# If the question does not appear within the configured time-out, the script
# terminates with a failure.
#
# Example usage:
#   accept_default_for "What's in a name?"
# Arguments:
#   question (string) The question that is expected to appear on screen.
proc accept_default_for { question } {
    expect {
        -exact $question { send "\n" }
        timeout { timed_out $question }
        eof { early_eof $question }
    }
}

# Asserts that the program will exit within the configured time-out. If it
# doesn't, the script terminates with a failure. If the program doesn't exit with
# the given exit code, the expect script exits with the same exit code, or 1 if
# the exit code is 0.
proc exits_with expected_exit_code {
    puts "Waiting for command to terminate..."

    expect {
        eof     { puts "Test completed." }
        timeout {
            puts "Command failed to terminate in time. Perhaps there's still a question pending?"
            exit 1
        }
    }

    lassign [wait] pid spawnid os_error_flag value

    if {$os_error_flag == 0} {
        puts "Exit code: $value"
        if {$value != $expected_exit_code} {
            if {$value == 0} {
                exit 1
            }
            exit $value
        }
    } else {
        puts "Operating system error: $value"
        exit 1
    }
}

# SCRIPT #
