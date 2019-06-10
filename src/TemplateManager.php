<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Entity\User;
use App\Repository\DestinationRepository;
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
        $quote = (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote && $destination = DestinationRepository::getInstance()->getById($quote->destinationId)) {
            $site = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationLink = sprintf('%s/%s/quote/%d', $site->url, $destination->countryName, $quote->id);

            $text = str_replace('[quote:destination_link]', $destinationLink, $text);
            $text = str_replace('[quote:destination_name]', $destination->countryName, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        if ($quote) {
            $text = str_replace('[quote:summary_html]', Quote::renderHtml($quote), $text);
            $text = str_replace('[quote:summary]', Quote::renderText($quote), $text);
        }

        /*
         * USER
         * [user:*]
         */
        $_user = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : ApplicationContext::getInstance()->getCurrentUser();

        if ($_user) {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
