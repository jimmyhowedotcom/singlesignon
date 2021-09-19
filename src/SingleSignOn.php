<?php

namespace JimmyHoweDotCom\SingleSignOn;

/**
 * @package JimmyHoweDotCom\SingleSignOn
 */
class SingleSignOn
{
    /**
     * Get the SSO host.
     *
     * @return string|null
     */
    public function getHost(): ?string
    {
        return config('sso.host');
    }
}
