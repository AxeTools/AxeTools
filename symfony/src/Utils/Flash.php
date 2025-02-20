<?php

namespace App\Utils;

final class Flash {
    /** @deprecated This should not be used, please use ALERT_INFO instead */
    public const ALERT_PRIMARY = 'primary';
    /** @deprecated This should not be used, please use ALERT_INFO instead */
    public const ALERT_SECONDARY = 'secondary';

    public const ALERT_DANGER = 'danger';
    public const ALERT_ERROR = 'danger';
    public const ALERT_WARNING = 'warning';
    public const ALERT_INFO = 'info';
    public const ALERT_SUCCESS = 'success';

    /** @deprecated This should not be used */
    public const ALERT_LIGHT = 'light';
    /** @deprecated This should not be used */
    public const ALERT_DARK = 'dark';
}
