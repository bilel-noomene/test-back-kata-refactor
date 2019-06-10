<?php

namespace App;

use App\Entity\Template;
use App\Helper\ClassFinder;
use App\TemplateExtension\TemplateExtensionInterface;

class TemplateManager
{
    /**
     * @var TemplateExtensionInterface[]
     */
    private static $extensions;

    /**
     * Replace placeholders in the template with the corresponding data.
     *
     * @param Template $template
     * @param array $inputData
     * @return Template
     * @throws \ReflectionException
     */
    public function getTemplateComputed(Template $template, array $inputData)
    {
        $placeholders = [];
        $data = [];

        foreach ($this->getExtensions() as $extension) {
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

    /**
     * Replace placeholders in the text with the corresponding data.

     * @param $text
     * @param array $placeholders
     * @param array $data
     * @return mixed
     */
    private function computeText($text, array $placeholders, array $data)
    {
        foreach ($placeholders as $placeholder) {
            if (array_key_exists($placeholder, $data)) {
                $text = str_replace($placeholder, $data[$placeholder], $text);
            }
        }

        return $text;
    }

    /**
     * Load the available template extensions.
     *
     * @return TemplateExtensionInterface[]
     * @throws \ReflectionException
     */
    private function getExtensions()
    {
        if (!self::$extensions) {
            self::$extensions = [];
            $namespace = (new \ReflectionClass(TemplateExtensionInterface::class))->getNamespaceName();

            $extensionsClasses = ClassFinder::getInstance()->findByInterface(TemplateExtensionInterface::class, $namespace, true);

            foreach ($extensionsClasses as $class) {
                self::$extensions[] = new $class;
            }
        }

        return self::$extensions;
    }
}
