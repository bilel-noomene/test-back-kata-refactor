<?php

namespace App\Helper;

use App\Entity\Quote;

/**
 * Format Quote to html or text
 */
class QuoteFormatter
{
    /**
     * Format Quote to html.
     *
     * @param Quote $quote
     * @return string
     */
    public static function renderHtml(Quote $quote): string
    {
        return '<p>' . $quote->getId() . '</p>';
    }

    /**
     * Format Quote to text.
     *
     * @param Quote $quote
     * @return string
     */
    public static function renderText(Quote $quote): string
    {
        return (string)$quote->getId();
    }
}
