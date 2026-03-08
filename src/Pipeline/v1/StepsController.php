<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Pipeline\v1;

use Coco\SourceWatcherApi\Framework\ApiResponse;
use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\ResponseCodes;
use Coco\SourceWatcherApi\Pipeline\StepsConfig;

class StepsController extends Controller
{
    use ApiResponse;

    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod !== 'GET') {
            $response = $this->notFoundResponse();
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        $steps = StepsConfig::getSteps();
        $response = $this->makeArrayResponse(ResponseCodes::OK, $steps);
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
}
