<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use Acelle\Library\StringHelper;

class DecodeHtmlSpecialChars implements StageInterface
{
    public function __invoke($html)
    {
        // Transform entities like "&amp;" back to "&"
        // Otherwise, URL may crash. For example:
        //
        //     <a href="example?name=Joe&age=20">
        //
        // might be transformed into:
        //
        //     <a href="example?name=Joe&amp;age=20">
        //
        return htmlspecialchars_decode($html);
    }
}
