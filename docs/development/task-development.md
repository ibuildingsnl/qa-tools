Developing tasks
================

## Context

[Tasks](../ubiquitous-language.md) describe actions to be performed on the target project, like installing a set of Composer packages, or writing an Ant build file. Tools, by means of Configurators, register these tasks for execution at a later stage.

Example registration of a task to install a Composer development dependency:

```php
final class PhpMdConfigurator implements Configurator
{
    public function configure(Interviewer $interviewer, TaskDirectory $taskDirectory, TaskHelperSet $taskHelperSet) {
        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('phpmd/phpmd', '^2.0'));
    }
}
```

Each task implements the marker interface `Ibuildings\QaTools\Core\Task\Task`; it does not have any methods. This allows the task to be registered with the `TaskDirectory` for later execution. During the execution stage, an executor picks up the task for execution. Each executor implements `Ibuildings\QaTools\Core\Task\Executor\Executor`. This interface has the following methods, which are, for simplicity's sake, always executed one after the other:

 * `supports(Task $task): bool`
 * `checkPrerequisites(TaskList $tasks, ...): void`
 * `execute(TaskList $tasks, ...): void`
 * `cleanUp(TaskList $tasks, ...): void`
 * `rollBack(TaskList $tasks, ...): void`

The execution process takes place as follows:

 0. Each executor's support for the tasks registered during the configuration stage is tested using the `support` method.
 0. The tasks that pass support test are then passed to the `checkPrerequisites` method. This method allows the executor check whether actually executing the task won't fail. For example, installing Composer package A, while one of the developer's dependencies conflicts with that package, would cause actually requiring the package to fail. By checking these prerequisites beforehand, the chance for the need for a rollback, or worse, the developer having to clean up their project, is reduced.
 0. When all prerequisites have been checked, each executor is asked to execute the tasks it supports.
 0. When all executors have executed their tasks, they are allowed to perform a clean-up. For example, while writing files, a backup may be kept on disk to allow for an automatic or user rollback.
 0. If an error occurs at any point during execution, an automatic rollback is attempted. This rollback instructs each executor to revert the work it has performed.
 
## Concrete steps to create a new task

 0. Create a new task implementation. For an example, look at `Ibuildings\QaTools\Core\Task\WriteFileTask`.
 0. Create a new executor implementation. For an example, look at `Ibuildings\QaTools\Core\Task\Executor\WriteFileTaskExecutor`.
 0. If you're up to the job, create a unit test for your executor implementation. Again, for an example, look at `Ibuildings\QaTools\UnitTest\Core\Task\Executor\WriteFileTaskExecutorTest`.
 0. Define your executor as a Symfony service in `src/Core/Resources/config/task_executors.yml`. Look to the other executors for examples, or read the Symfony Framework's documentation on [defining services] using YAML(http://symfony.com/doc/current/service_container.html#creating-configuring-services-in-the-container). Note that this documentation contains information about the Symfony Framework, while we only use its dependency injection component.
 0. Register your task executor with the tag `qa_tools.task_executor`.
 0. If you're up to the job, please [expand the system tests](writing-system-tests.md) to assert that your executor does it work properly. 
