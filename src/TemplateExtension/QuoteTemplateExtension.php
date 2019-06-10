<?php

namespace App\TemplateExtension;

use App\Entity\Quote;
use App\Helper\QuoteFormatter;
use App\Helper\SingletonTrait;
use App\Repository\DestinationRepository;
use App\Repository\SiteRepository;

/**
 * Template extension for quote placeholders.
 */
class QuoteTemplateExtension extends AbstractTemplateExtension
{
    use SingletonTrait;

    const TAG_SUMMARY = 'summary';
    const TAG_SUMMARY_HTML = 'summary_html';
    const TAG_DESTINATION_NAME = 'destination_name';
    const TAG_DESTINATION_LINK = 'destination_link';

    /**
     * {@inheritdoc}
     */
    protected function getPrefix()
    {
        return 'quote';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTags()
    {
        return [
            self::TAG_SUMMARY,
            self::TAG_SUMMARY_HTML,
            self::TAG_DESTINATION_NAME,
            self::TAG_DESTINATION_LINK,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function loadData(array $inputData)
    {
        $data = [];
        $quote = (isset($inputData['quote']) && $inputData['quote'] instanceof Quote) ? $inputData['quote'] : null;

        if ($quote && $destination = DestinationRepository::getInstance()->getById($quote->destinationId)) {
            $site = SiteRepository::getInstance()->getById($quote->siteId);

            $this->appendData($data, self::TAG_DESTINATION_LINK, sprintf('%s/%s/quote/%d', $site->url, $destination->countryName, $quote->id));
            $this->appendData($data, self::TAG_DESTINATION_NAME, $destination->countryName);
        } else {
            $this->appendData($data, self::TAG_DESTINATION_LINK, '');
        }

        if ($quote) {
            $this->appendData($data, self::TAG_SUMMARY, QuoteFormatter::renderText($quote));
            $this->appendData($data, self::TAG_SUMMARY_HTML, QuoteFormatter::renderHtml($quote));
        }

        return $data;
    }
}