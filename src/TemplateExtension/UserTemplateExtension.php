<?php

namespace App\TemplateExtension;

use App\Context\ApplicationContext;
use App\Entity\User;
use App\Helper\SingletonTrait;

/**
 * Template extension for user placeholders.
 */
class UserTemplateExtension extends AbstractTemplateExtension
{
    use SingletonTrait;

    private const TAG_FIRST_NAME = 'first_name';

    /**
     * {@inheritdoc}
     */
    protected function getPrefix(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTags(): array
    {
        return [self::TAG_FIRST_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function loadData(array $inputData): array
    {
        $data = [];

        if (!(($user = $data['user'] ?? null) instanceof User)) {
            $user = ApplicationContext::getInstance()->getCurrentUser();
        }

        if ($user) {
            $this->appendData($data, self::TAG_FIRST_NAME, ucfirst(mb_strtolower($user->getFirstName())));
        }

        return $data;
    }
}