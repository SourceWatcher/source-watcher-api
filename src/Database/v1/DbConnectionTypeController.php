<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Database\v1;

use Coco\SourceWatcherApi\Database\DbConnectionTypeDAO;
use Coco\SourceWatcherApi\Framework\ApiResponse;
use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\Exception;
use Coco\SourceWatcherApi\Framework\ResponseCodes;

class DbConnectionTypeController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        parent::__construct();
    }

    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod == 'GET') {
            $response = $this->getDbConnectionTypeList();
        } else {
            $response = $this->notFoundResponse();
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getDbConnectionTypeList(): array
    {
        try {
            $dao = new DbConnectionTypeDAO();
            return $this->makeArrayResponse(ResponseCodes::OK, $dao->getDbConnectionType());
        } catch (Exception $exception) {
            return ["status_code_header" => ResponseCodes::INTERNAL_SERVER_ERROR, "body" => $exception->getMessage()];
        }
    }
}
