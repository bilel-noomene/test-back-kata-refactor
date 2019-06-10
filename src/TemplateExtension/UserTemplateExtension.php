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

    const TAG_FIRST_NAME = 'first_name';

    /**
     * {@inheritdoc}
     */
    protected function getPrefix()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTags()
    {
        return [self::TAG_FIRST_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function loadData(array $inputData)
    {
        $data = [];
        $user = (isset($data['user']) && $inputData['user'] instanceof User) ? $inputData['user'] : ApplicationContext::getInstance()->getCurrentUser();

        if ($user) {
            $this->appendData($data, self::TAG_FIRST_NAME, ucfirst(mb_strtolower($user->firstname)));
        }

        return $data;
    }
}