<?php

namespace Coco\SourceWatcherApi\Framework;

/**
 * Class ResponseCodes
 * @package Coco\SourceWatcherApi\Framework
 */
class ResponseCodes
{
    /**
     *
     */
    const BAD_REQUEST = "HTTP/1.1 400 Bad Request";

    /**
     *
     */
    const INTERNAL_SERVER_ERROR = "HTTP/1.1 500 Internal Server Error";

    /**
     *
     */
    const NOT_FOUND = "HTTP/1.1 404 Not Found";

    /**
     *
     */
    const OK = "HTTP/1.1 200 OK";
}
