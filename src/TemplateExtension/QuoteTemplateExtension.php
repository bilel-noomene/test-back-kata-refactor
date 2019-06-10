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

    private const TAG_SUMMARY = 'summary';
    private const TAG_SUMMARY_HTML = 'summary_html';
    private const TAG_DESTINATION_NAME = 'destination_name';
    private const TAG_DESTINATION_LINK = 'destination_link';

    /**
     * {@inheritdoc}
     */
    protected function getPrefix(): string
    {
        return 'quote';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTags(): array
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
    public function loadData(array $inputData): array
    {
        $data = [];
        $quote = (isset($inputData['quote']) && $inputData['quote'] instanceof Quote) ? $inputData['quote'] : null;

        if ($quote && $destination = DestinationRepository::getInstance()->getById($quote->getDestinationId())) {
            $site = SiteRepository::getInstance()->getById($quote->getSiteId());

            $this->appendData($data, self::TAG_DESTINATION_LINK, sprintf('%s/%s/quote/%d', $site->getUrl(), $destination->getCountryName(), $quote->getId()));
            $this->appendData($data, self::TAG_DESTINATION_NAME, $destination->getCountryName());
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