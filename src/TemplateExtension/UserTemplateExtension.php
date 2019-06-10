<?php

namespace App\TemplateExtension;

use App\Context\ApplicationContext;
use App\Entity\Template;
use App\Entity\User;
use App\Helper\SingletonTrait;

/**
 * Template extension for user placeholders.
 */
class UserTemplateExtension implements TemplateExtensionInterface
{
    use SingletonTrait;

    private $placeholders = ['[user:first_name]'];

    /**
     * {@inheritdoc}
     */
    public function isInvolved(Template $template)
    {
        return preg_match('/\[user:first_name\]/', $template->subject . $template->content) === 1;
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
        $user = (isset($data['user']) && $inputData['user'] instanceof User) ? $inputData['user'] : ApplicationContext::getInstance()->getCurrentUser();

        if ($user) {
            $data['[user:first_name]'] = ucfirst(mb_strtolower($user->firstname));
        }

        return $data;
    }
}