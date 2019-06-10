<?php

namespace App\TemplateExtension;

use App\Entity\Quote;
use App\Entity\Template;
use App\Helper\SingletonTrait;
use App\Repository\DestinationRepository;
use App\Repository\SiteRepository;

/**
 * Template extension for quote placeholders.
 */
class QuoteTemplateExtension implements TemplateExtensionInterface
{
    use SingletonTrait;

    private $placeholders = [
        '[quote:summary_html]',
        '[quote:summary]',
        '[quote:destination_name]',
        '[quote:destination_link]',
    ];

    /**
     * {@inheritdoc}
     */
    public function isInvolved(Template $template)
    {
        $text = $template->subject . $template->content;

        return preg_match('/\[quote:(summary_html|summary|destination_name|destination_link)\]/', $text) === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
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

            $data['[quote:destination_link]'] = sprintf('%s/%s/quote/%d', $site->url, $destination->countryName, $quote->id);
            $data['[quote:destination_name]'] = $destination->countryName;
        } else {
            $data['[quote:destination_link]'] = '';
        }

        if ($quote) {
            $data['[quote:summary_html]'] = Quote::renderHtml($quote);
            $data['[quote:summary]'] = Quote::renderText($quote);
        }

        return $data;
    }
}