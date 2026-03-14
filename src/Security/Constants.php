<?php

namespace Coco\SourceWatcherApi\Security;

class Constants
{
    public const ALGORITHM = 'RS256';
    /** Access token validity (JWT payload). Keep moderate for security; board refreshes when &lt; 5 min left. */
    public const JWT_EXPIRATION_TIME = '+30 min';
}
