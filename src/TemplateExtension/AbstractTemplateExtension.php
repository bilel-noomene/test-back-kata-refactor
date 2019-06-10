<?php

namespace App\TemplateExtension;

use App\Entity\Template;

/**
 * Abstract class for template extensions.
 */
abstract class AbstractTemplateExtension implements TemplateExtensionInterface
{
    /**
     * Return the prefix used in placeholders.
     *
     * @return string
     */
    abstract protected function getPrefix(): string;

    /**
     * Return the tags used in placeholders.
     * @return string[]
     */
    abstract protected function getTags(): array;

    /**
     * {@inheritdoc}
     */
    public function isInvolved(Template $template): bool
    {
        $pattern = sprintf('/\[%s:(%s)\]/', $this->getPrefix(), implode('|', $this->getTags()));

        return (preg_match($pattern, $template->getSubject() . $template->getContent()));
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholders(): array
    {
        $placeholders = [];

        foreach ($this->getTags() as $tag) {
            $placeholders[] = $this->composePlaceholder($tag);
        }

        return $placeholders;
    }

    /**
     * Compose the placeholder that correspond to tag.
     *
     * @param string $tag
     * @return string
     */
    protected function composePlaceholder($tag): string
    {
        return sprintf('[%s:%s]', $this->getPrefix(), $tag);
    }

    /**
     * Append the value to data array with the placeholder key.
     *
     * @param array $data
     * @param string $tag
     * @param mixed $value
     */
    protected function appendData(array &$data, $tag, $value): void
    {
        $data[$this->composePlaceholder($tag)] = $value;
    }
}