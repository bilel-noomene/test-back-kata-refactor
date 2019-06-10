<?php

namespace App\TemplateExtension;

use App\Entity\Template;

/**
 * Interface dor adding support for new placeholders in the a template.
 */
interface TemplateExtensionInterface
{
    /**
     * Check if the extension should be used to handle the template.
     *
     * @param Template $template
     * @return bool
     */
    public function isInvolved(Template $template): bool;

    /**
     * Return the list of placeholders added by the extension.
     *
     * @return string[]
     */
    public function getPlaceholders(): array;

    /**
     * Load the data that correspond to the placeholders.
     *
     * @param array $inputData
     * @return array
     */
    public function loadData(array $inputData): array;
}
