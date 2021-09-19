<?php

namespace JimmyHoweDotCom\SingleSignOn\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @package JimmyHoweDotCom\SingleSignOn
 */
class SingleSignOn extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sso';
    }
}
