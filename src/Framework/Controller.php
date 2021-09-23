<?php

namespace Coco\SourceWatcherApi\Framework;

/**
 * Class Controller
 * @package Coco\SourceWatcherApi\Framework
 */
abstract class Controller
{
    /**
     * @var array
     */
    protected array $requestData;

    /**
     * Controller constructor.
     */
    public function __construct()
    {

    }

    /**
     * Allows getting the request data.
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * Allows setting the request data.
     * @param array $requestData
     */
    public function setRequestData(array $requestData): void
    {
        $this->requestData = $requestData;
    }

    /**
     * This method handles the request.
     * @param string $requestMethod
     * @param array $extraOptions
     */
    public abstract function processRequest(string $requestMethod, array $extraOptions): void;

    /**
     * This method is used to send a not found response.
     * @return array
     */
    protected function notFoundResponse(): array
    {
        $response["status_code_header"] = ResponseCodes::NOT_FOUND;
        $response["body"] = null;

        return $response;
    }
}
