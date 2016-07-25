Developing a QA tool
====================

## Adding Tool and Configurators
One of the QA Tools' objectives is to make it easy to configure tools.
These tools reside in the `src/Tool` directory.
To add a Tool, create a new directory in `src/Tool` and give it a descriptive name, preferably the actual name of the tool.
In this new directory, create a class with the same name that extends `\Ibuildings\QaTools\Core\Tool\Tool`. 
This class should be added to the `\Ibuildings\QaTools\Core\Application\Application::getRegisteredTools()` 
method in order for it to be configurable.

Configurators are responsible for configuring a tool for a specific project type.
By default, the tool's configurators are configured in its `src/Tool/<ToolName>/Resources/config/configurators.yml` file. 
To modify which configuration file is used or to add configuration files for this tool, 
override the inherited `getConfigurationFiles` method.

Configurators should be added under the tool's namespace (i.e. `\Ibuildings\QaTools\Tool\<ToolName>\Configurator\<ConfiguratorName>`) and
are defined as a service through the tool's `configurators.yml` file. 
A tag has to be added to the configurator's definition to indicate which project types it supports:

```yaml
services:
    qa_tools.tool.configurator.<configurator_name>:
        class: Ibuildings\QaTools\Tool\<ToolName>\Configurator\<ConfiguratorName>
        tags:
            - name: qa_tools.tool
              project_type: <project_type>
```

A finite list of supported project types are hard-coded in `\Ibuildings\QaTools\Core\Project\ProjectType`.
Which project types apply for a certain project are determined through the interview process using the `ProjectConfigurator`.

The configuration process can be defined in the Configurator's `configure`
method, in which it can use the `Interviewer`, `TaskRegistry` and
`TaskHelperSet`. Templating and such are available through the `TaskHelperSet`.
These can be used to help register tasks with the `TaskRegistry`. A tool's
templates should reside under `src/Tool/<ToolName>/Resources/templates`.

The `Interviewer` is used for IO interaction.

## Interview API
The `Interviewer` is responsible for the interaction with the developer. It can provide the developer with information using the
`tell` and `warn` methods and acquire information from him using the `ask` method. While the `tell` and `warn` methods
accept strings as their arguments, the `ask` method is requires a specific `Question`.

### TextualQuestion
A `TextualQuestion` is an open question that expects a `TextualAnswer` as a response. 
Optionally, a `TextualAnswer` can be given as a default answer as a second argument.

```php
$answer = $interviewer->ask(new TextualQuestion('What is your favorite food?', new TextualAnswer('Pizza'));
```

Alternatively, a `TextualQuestion` can be created as using a factory:
```php
$answer = $interviewer->ask(Question::create('What is your favorite food?', 'Pizza'));
```

### YesOrNoQuestion
A `YesOrNoQuestion` is a closed question that expects a `YesOrNoAnswer` as a response. 
Optionally, a `YesOrNoAnswer` can be given as a default answer as a second argument.
Since there are only two possible answers, `YesOrNoAnswers` are created using named constructors. 

```php
$answer = $interviewer->ask(new YesOrNoQuestion('Do you like Mexican food?', YesOrNoAnswer::yes());
```

Alternatively, a `YesOrNoQuestion` can be created as using a factory:
```php
$answer = $interviewer->ask(Question::createYesOrNo('Do you like Mexican food?', YesOrNoAnswer::YES));
```

### MultipleChoiceQuestion
A `MultipleChoiceQuestion` is a closed question that expects a `TextualAnswer` selected from a list of `Choices`, 
its second argument, as a response. 
Optionally, a `TextualAnswer` can be given as a default answer as a third argument.

```php
$answer = $interviewer->ask(new MultipleChoiceQuestion('What would you like to eat?', 
    new Choices([
        new TextualAnswer('Caesar Salad'),
        new TextualAnswer('Cheese Sandwich'),
        new TextualAnswer('Chicken Wings')
    ]),
    new TextualAnswer('Chicken Wings')
));
```

Alternatively, a `MultipleChoiceQuestion` can be created as using a factory:
```php
$answer = $interviewer->ask(Question::createMultipleChoice('What would you like to eat?', [
    'Caesar Salad',
    'Cheese Sandwich',
    'Chicken Wings'
],
    'Chicken Wings'
));
```

### ListChoiceQuestion
A `ListChoiceQuestion` is a closed question that expects `Choices` selected from a list of `Choices`, 
its second argument, as a response. 
Optionally, `Choices` can be given as a default answer as a third argument.

```php
$answer = $interviewer->ask(new ListChoiceQuestion('Which of the following would you like to eat next?', 
    new Choices([
        new TextualAnswer('Caesar Salad'),
        new TextualAnswer('Cheese Sandwich'),
        new TextualAnswer('Chicken Wings')
    ]),
    new Choices([
        new TextualAnswer('Caesar Salad'),
        new TextualAnswer('Cheese Sandwich'),
    ])
));
```

Alternatively, a `ListChoiceQuestion` can be created as using a factory:
```php
$answer = $interviewer->ask(Question::createMultipleChoice('Which of the following would you like to eat next?', [
    'Caesar Salad',
    'Cheese Sandwich',
    'Chicken Wings'
],
    'Caesar Salad',
    'Chick Wings'
));
```
