<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use Acelle\Library\StringHelper;
use Soundasleep\Html2Text;

class AddPreheader implements StageInterface
{
    public $preheader;

    public function __construct($preheader)
    {
        if (!is_null($preheader)) {
            // trim() does not work with null param
            $this->preheader = trim($preheader);
        }
    }

    public function __invoke($html)
    {
        if (empty($this->preheader)) {
            return $html;
        }

        return StringHelper::updateHtml($html, function ($dom) {
            $body = $dom->getElementsByTagName('body')->item(0);

            // Convert preheader to PLAIN
            $plain = Html2Text::convert($this->preheader, [ 'ignore_errors' => true ]);
            $plain = htmlentities($plain);

            $e = $dom->createElement('div', $plain);
            $e->setAttribute('style', 'display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden');

            if ($body->hasChildNodes()) {
                $body->insertBefore($e, $body->firstChild);
            } else {
                $body->appendChild($e);
            }
        });
    }
}
