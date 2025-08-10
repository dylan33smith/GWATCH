<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_logged_in', [$this, 'isLoggedIn']),
            new TwigFunction('get_username', [$this, 'getUsername']),
        ];
    }

    public function isLoggedIn(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $session = $request->getSession();
        return $session && $session->has('user_id');
    }

    public function getUsername(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $session = $request->getSession();
        return $session ? $session->get('username') : null;
    }
}
