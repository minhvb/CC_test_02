<?php

namespace Application\Utils;

/**
 * Class BrowserHelper
 * @package Application\Utils
 */
class BrowserHelper
{
    const CHROME = 'Chrome';
    const Opera = 'Opera';
    const INTERNET_EXPLORER = 'Internet Explorer';
    const EDGE = 'Edge';
    const SAFARI = 'Safari';
    const FIREFOX = 'Firefox';
    const OTHER = 'Other';

    public static function getBrowserName($user_agent)
    {
        if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
        elseif (strpos($user_agent, 'Edge')) return 'Edge';
        elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
        elseif (strpos($user_agent, 'Safari')) return 'Safari';
        elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
        elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';

        return 'Other';
    }
}
