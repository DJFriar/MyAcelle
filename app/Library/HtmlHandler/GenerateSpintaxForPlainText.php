<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use bjoernffm\Spintax\Parser;

class GenerateSpintaxForPlainText implements StageInterface
{
    public function __invoke($text)
    {
        return Parser::replicate($text, []);
    }

    private function containsSpintaxPattern($text)
    {
        // REGEXP to check if a text contains Spintax {}
        $containsSpintaxRegexp = '/{.+|.+}/';
        return preg_match($containsSpintaxRegexp, $text) == true;
    }
}
