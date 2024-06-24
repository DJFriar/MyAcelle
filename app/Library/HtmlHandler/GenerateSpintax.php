<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use bjoernffm\Spintax\Parser;
use Acelle\Library\StringHelper;

class GenerateSpintax implements StageInterface
{
    public function __invoke($html)
    {
        // Introduction: Spintax parser may strip brackets ("{}") in HTML which contains CSS
        // As a result, we need to handle it by extracting text from the HTML

        // Also, remember to exclude the <STYLE> tag for Spintax
        $cleanedHtml = StringHelper::updateHtml($html, function ($dom) {
            $styles = $dom->getElementsByTagName('style');
            for ($i = $styles->length; --$i >= 0;) {
                $node = $styles->item($i);
                $node->parentNode->removeChild($node);
            }
        });

        // Also, remember to exclude the <STYLE> tag for Spintax
        $html = StringHelper::updateHtml($html, function ($dom) {
            // Do nothing
            // Just to make sure the content of $cleanedHtml and $html are exactly the same (standardized by DOMDocument)
            // The only difference is that <STYLE> tags are removed from $cleanedHtml
        });

        // Get all text content of the HTML
        // Look after a ">" and before a "<", with any char that is not ">" and "<"
        $htmlTextRegexp = '/(?<=>)[^<>]+(?=<)/';

        // Extract text values from HTML, do not count <STYLE> tags
        preg_match_all($htmlTextRegexp, $cleanedHtml, $matches);

        // Check every value
        foreach ($matches[0] as $text) {
            if ($this->containsSpintaxPattern($text)) {
                $transformed = Parser::replicate($text, []);

                // Actually replace in the HTML content
                $html = str_replace(">{$text}<", ">{$transformed}<", $html); // Notice: use $html here, not $cleanedHtml
            }
        }

        return $html;
    }

    private function containsSpintaxPattern($text)
    {
        // REGEXP to check if a text contains Spintax {}
        $containsSpintaxRegexp = '/{.+|.+}/';
        return preg_match($containsSpintaxRegexp, $text) == true;
    }
}
