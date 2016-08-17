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
 the developer's answers, the configurator adds requirements to the requirements
 directory. These requirements and how they are executed are defined in the core
 of QA Tools; tools cannot create new types of requirements. Examples of
 requirements are the presence of a Composer package or configuration file.
 0. **To be done** A sorted task list is compiled from the requirements
 registered with the requirements directory in the previous step.
 0. **To be done** The requirements' prerequisites are checked. An example would
 be checking whether the required Composer packages don't conflict with any
 installed packages.
 0. **To be done** The tasks are executed. If a task fails, all successful tasks
 are rolled  back.
 0. The project settings and the answers given to each question are stored in a
 configuration file. Based on this configuration file, all question's are
 pre-filled on a subsequent run of the QA Tools.
 
This process is managed in the [`ConfigurationService`][src-config-service].

[src-config-service]: ../../src/Core/Service/ConfigurationService.php
