Configuration process
=====================

The QA Tools configuration process is as follows:

 0. The developer is "interviewed" the project's name, type, whether the
 developer uses Travis, and where all the tools' configuration files ought to be
 stored. These answers influence the type of configuration that takes place
 directly afterwards.
 0. Based on the project settings (eg. project type), a run list of tool
 configurators is compiled. Each configurator gets the chance to interview the
 developer for more settings pertinent to each configurator's tool. Based on
 the developer's answers, the configurator adds tasks to the task directory.
 These tasks and how they are executed are defined in the core of QA Tools;
 tools cannot create new types of tasks. Examples of tasks are installing a
 Composer package and writing a configuration file for a tool.
 0. **To be done** A sorted task list is compiled from the tasks registered with
 the task directory in the previous step.
 0. **To be done** The tasks' prerequisites are checked – they are "dry-run". An
 example would be checking for the existence and writability of a
 `composer.json` file.
 0. **To be done** The tasks are executed. If a task fails, all tasks are rolled
 back.
 0. The project settings and the answers given to each question are stored in a
 configuration file. Based on this configuration file, all question's are
 pre-filled on a subsequent run of the QA Tools.
 
This process is managed in the [`ConfigurationService`][src-config-service].

[src-config-service]: ../../src/Core/Service/ConfigurationService.php
