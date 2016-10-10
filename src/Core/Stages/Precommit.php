<?php
namespace Ibuildings\QaTools\Core\Stages;

final class Precommit implements Stage
{
    public function identifier()
    {
        return 'precommit';
    }
}
