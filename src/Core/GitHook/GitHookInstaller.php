<?php

namespace Ibuildings\QaTools\Core\GitHook;

use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Ibuildings\QaTools\Core\Project\Directory;

final class GitHookInstaller
{
    const PRE_COMMIT_PATH = '.git/hooks/pre-commit';

    /**
     * @var FileHandler
     */
    private $fileHandler;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    public function installPreCommitHook(Interviewer $interviewer, Directory $projectRoot)
    {
        $filePath = $projectRoot->getDirectory().self::PRE_COMMIT_PATH;

        if ($this->fileHandler->exists($filePath)) {
            /** @var YesOrNoAnswer $overwrite */
            $overwrite = $interviewer->ask(
                QuestionFactory::createYesOrNo(
                    'A pre-commit hook already exists in this project. Are you sure you want to overwrite it?',
                    YesOrNoAnswer::NO
                )
            );

            if ($overwrite->is(false)) {
                $interviewer->notice(
                    'The pre-commit hook was left unchanged. You can manually add `ant precommit` to your '.
                    'pre-commit hook in order to run the pre-commit build before every commit.'
                );

                return;
            }
        }

        $this->fileHandler->writeTo($filePath, $this->fileHandler->readFrom(__DIR__.'/files/pre-commit'));
        $this->fileHandler->changeMode($filePath, 0775);

        $interviewer->success(sprintf('Installed Git pre-commit hook in %s.', $filePath));
    }
}
