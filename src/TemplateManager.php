<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Entity\User;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;

class TemplateManager
{
    public function getTemplateComputed(Template $template, array $data)
    {
        $replaced = clone($template);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $site = SiteRepository::getInstance()->getById($quote->siteId);
            $destination = DestinationRepository::getInstance()->getById($quote->destinationId);

            if ($this->hasPlaceholder($text, '[quote:destination_link]') && $destination) {
                $destinationLink = sprintf('%s/%s/quote/%d', $site->url, $destination->countryName, $_quoteFromRepository->id);
                $text = str_replace('[quote:destination_link]', $destinationLink, $text);
            }

            if ($this->hasPlaceholder($text, '[quote:summary_html]')) {
                $text = str_replace('[quote:summary_html]', Quote::renderHtml($_quoteFromRepository), $text);
            }

            if ($this->hasPlaceholder($text, '[quote:summary]')) {
                $text = str_replace('[quote:summary]', Quote::renderText($_quoteFromRepository), $text);
            }

            if ($this->hasPlaceholder($text, '[quote:destination_name]')) {
                // There is no check that $destination is not null as in the case of '[quote:destination_link]'
                $text = str_replace('[quote:destination_name]', $destination->countryName, $text);
            }
        }

        if (!isset($destination)) {
            // Considering that $destination is merged with $destinationOfQuote, the previous (!isset($destination)) is
            //  now equal to (!($quote && $this->hasPlaceholder($text, '[quote:destination_link]') && isset($destination)))
            //  or (!$quote || !$this->hasPlaceholder($text, '[quote:destination_link]') || !isset($destination)))).
            // Because replacing '[quote:destination_link]' when (!$this->hasTag($text, '[quote:destination_link]')) has
            //  no effect and  (!isset($destination)) is true when (!$quote) is true, the condition is simplified to
            //  !isset($destination).
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        /*
         * USER
         * [user:*]
         */
        $_user = (isset($data['user']) && ($data['user'] instanceof User)) ? $data['user'] : $APPLICATION_CONTEXT->getCurrentUser();

        if ($_user && $this->hasPlaceholder($text, '[user:first_name]')) {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }

    private function hasPlaceholder($text, $placeholder)
    {
        return strpos($text, $placeholder) !== false;
    }
}
