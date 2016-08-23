Configuration process
=====================

The QA Tools configuration process is as follows:

 0. The developer is "interviewed" the project's name, type, whether the
 developer uses Travis, and where all the tools' configuration files ought to be
 stored. These answers influence the type of configuration that takes place
 directly afterwards.
 0. Based on the project settings (eg. project type), a list of tool
 configurators is compiled. Each configurator gets the chance to interview the
 developer for more settings pertinent to each configurator's tool. Based on
 the developer's answers, the configurator adds tasks to the task directory.
 These tasks and how they are executed are defined in the core of QA Tools;
 tools cannot create new types of tasks. Examples of tasks are the installation
 of a Composer development dependency or the writing of a tool's configuration
 file.
 0. In the following execution stage, each task executor is tested for tasks it
 supports. Each executor's supported tasks are then passed to each executors
 various stages.
 0. The tasks' prerequisites are checked by each executor. An example would
 be checking whether the required Composer packages don't conflict with any
 installed packages.
 0. The tasks are executed. If a task fails, tasks that have already been
 executed are rolled back in reverse order.
 0. Each executor gets the chance to clean up, like discarding any backups of
 files it was to write.
 0. The project settings and the answers given to each question are stored in a
 configuration file. Based on this configuration file, all question's are
 pre-filled on a subsequent run of the QA Tools.
 
This process is managed in the [`ConfigurationService`][src-config-service].

[src-config-service]: ../../src/Core/Service/ConfigurationService.php
