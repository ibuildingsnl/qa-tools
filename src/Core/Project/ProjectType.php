<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\LogicException;

final class ProjectType
{
    const TYPE_PHP_SF_2     = 'php.sf2';
    const TYPE_PHP_SF_3     = 'php.sf3';
    const TYPE_PHP_DRUPAL_7 = 'php.drupal7';
    const TYPE_PHP_DRUPAL_8 = 'php.drupal8';
    const TYPE_PHP_OTHER    = 'php.other';

    const TYPE_JS_ANGULAR_1 = 'js.angular1';
    const TYPE_JS_ANGULAR_2 = 'js.angular2';
    const TYPE_JS_OTHER     = 'js.other';

    private static $humanReadableTypes = [
        self::TYPE_PHP_SF_2     => 'symfony 2',
        self::TYPE_PHP_SF_3     => 'symfony 3',
        self::TYPE_PHP_DRUPAL_7 => 'drupal 7',
        self::TYPE_PHP_DRUPAL_8 => 'drupal 8',
        self::TYPE_PHP_OTHER    => 'other php project',
        self::TYPE_JS_ANGULAR_1 => 'angularjs 1',
        self::TYPE_JS_ANGULAR_2 => 'angular 2',
        self::TYPE_JS_OTHER     => 'other js project',
    ];

    /**
     * @var string
     */
    private $projectType;

    public function __construct($projectType)
    {
        Assertion::choice($projectType, [
            self::TYPE_PHP_OTHER,
            self::TYPE_PHP_SF_2,
            self::TYPE_PHP_SF_3,
            self::TYPE_PHP_DRUPAL_7,
            self::TYPE_PHP_DRUPAL_8,
            self::TYPE_JS_OTHER,
            self::TYPE_JS_ANGULAR_1,
            self::TYPE_JS_ANGULAR_2,
        ]);

        $this->projectType = $projectType;
    }

    public static function fromHumanReadableString($humanReadableType)
    {
        Assertion::string($humanReadableType);

        $types = array_combine(
            array_values(self::$humanReadableTypes),
            array_keys(self::$humanReadableTypes)
        );
        $typeKey = strtolower($humanReadableType);

        if (!isset($types[$typeKey])) {
            throw new LogicException(sprintf(
                'Cannot convert "%s" to an existing ProjectType. It has to be one of the following: %s',
                $humanReadableType,
                implode(', ', self::$humanReadableTypes)
            ));
        }

        return new ProjectType($types[$typeKey]);
    }

    public function toHumanReadableString()
    {
        return self::$humanReadableTypes[$this->projectType];
    }

    public function getProjectType()
    {
        return $this->projectType;
    }
}
