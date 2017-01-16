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
    const PRE_PUSH_PATH = '.git/hooks/pre-push';

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
        $this->installHook(
            $interviewer,
            $projectRoot->getDirectory().self::PRE_COMMIT_PATH,
            'pre-commit'
        );
    }

    public function installPrePushHook(Interviewer $interviewer, Directory $projectRoot)
    {
        $this->installHook(
            $interviewer,
            $projectRoot->getDirectory().self::PRE_PUSH_PATH,
            'pre-push'
        );
    }

    private function installHook(Interviewer $interviewer, $filePath, $hookName)
    {
        $hookContents = $this->fileHandler->readFrom(sprintf('%s/files/%s', __DIR__, $hookName));

        if ($this->fileHandler->exists($filePath)) {
            /** @var YesOrNoAnswer $overwrite */
            $overwrite = $interviewer->ask(
                QuestionFactory::createYesOrNo(
                    sprintf(
                        'A %s hook already exists in this project. Are you sure you want to overwrite it?',
                        $hookName
                    ),
                    YesOrNoAnswer::NO
                )
            );

            if ($overwrite->is(YesOrNoAnswer::NO)) {
                $interviewer->notice(
                    sprintf(
                        'The %s hook was left unchanged. You can manually add the following to your %s hook:'."\n\n".
                        '%s'."\n",
                        $hookName,
                        $hookName,
                        implode(
                            "\n",
                            array_filter(
                                explode("\n", $hookContents),
                                function ($line) {
                                    return trim($line) !== '' && 0 !== strpos($line, '#!');
                                }
                            )
                        )
                    )
                );

                return;
            }
        }

        $this->fileHandler->writeTo(
            $filePath,
            $hookContents
        );
        $this->fileHandler->changeMode($filePath, 0775);

        $interviewer->success(sprintf('Installed Git %s hook in %s.', $hookName, $filePath));
    }
}
