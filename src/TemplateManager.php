<?php

namespace App;

use App\Entity\Template;
use App\TemplateExtension\QuoteTemplateExtension;
use App\TemplateExtension\TemplateExtensionInterface;
use App\TemplateExtension\UserTemplateExtension;

class TemplateManager
{
    private $extensions;

    public function __construct()
    {
        $this->extensions = [
            UserTemplateExtension::getInstance(),
            QuoteTemplateExtension::getInstance(),
        ];
    }

    public function getTemplateComputed(Template $template, array $inputData)
    {
        $placeholders = [];
        $data = [];

        /** @var TemplateExtensionInterface $extension */
        foreach ($this->extensions as $extension) {
            if ($extension->isInvolved($template)) {
                $placeholders = array_merge($placeholders, $extension->getPlaceholders());
                $data = array_merge($data, $extension->loadData($inputData));
            }
        }

        $replaced = clone($template);
        $replaced->subject = $this->computeText($replaced->subject, $placeholders, $data);
        $replaced->content = $this->computeText($replaced->content, $placeholders, $data);

        return $replaced;
    }

    private function computeText($text, array $placeholders, array $data)
    {
        foreach ($placeholders as $placeholder) {
            if (array_key_exists($placeholder, $data)) {
                $text = str_replace($placeholder, $data[$placeholder], $text);
            }
        }

        return $text;
    }
}
