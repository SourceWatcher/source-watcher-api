<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Pipeline\v1;

use Coco\SourceWatcherApi\Framework\ApiResponse;
use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\ResponseCodes;

/**
 * Transformations API:
 *
 * GET  /api/v1/transformation
 *     Returns list of saved transformation names (basenames of .swt files).
 *     Response: { "names": ["test07", "my-pipeline", ...] }
 *
 * POST /api/v1/transformation
 *     Save a transformation definition (.swt file) given a list of steps.
 *     Expected JSON: { "name": "optional", "steps": [ ... ] }
 */
class TransformationController extends Controller
{
    use ApiResponse;

    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod === 'GET') {
            $this->listTransformations();
            return;
        }
        if ($requestMethod !== 'POST') {
            $response = $this->notFoundResponse();
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }
        $this->saveTransformation();
    }

    private function getTransformationsDirectory(): ?string
    {
        $home = $_SERVER['HOME'] ?? getenv('HOME') ?: null;
        if (empty($home)) {
            $home = dirname(__DIR__, 3);
        }
        $mainDirectory = $home . DIRECTORY_SEPARATOR . '.source-watcher';
        $transformationsDirectory = $mainDirectory . DIRECTORY_SEPARATOR . 'transformations';
        return (is_dir($transformationsDirectory) || @mkdir($transformationsDirectory, 0777, true))
            ? $transformationsDirectory
            : null;
    }

    private function listTransformations(): void
    {
        $transformationsDirectory = $this->getTransformationsDirectory();
        if ($transformationsDirectory === null) {
            $response = $this->makeArrayResponse(ResponseCodes::OK, ['names' => []]);
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }
        $names = [];
        $files = @scandir($transformationsDirectory);
        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                if (substr($file, -4) === '.swt' && is_file($transformationsDirectory . DIRECTORY_SEPARATOR . $file)) {
                    $names[] = substr($file, 0, -4);
                }
            }
        }
        sort($names, SORT_STRING);
        $response = $this->makeArrayResponse(ResponseCodes::OK, ['names' => $names]);
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function saveTransformation(): void
    {

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody ?? '', true);

        if (!is_array($data)) {
            $response = $this->makeResponse(ResponseCodes::BAD_REQUEST, 'Invalid JSON payload.');
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        $steps = $data['steps'] ?? null;
        if (!is_array($steps) || $steps === []) {
            $response = $this->makeResponse(ResponseCodes::BAD_REQUEST, 'Field "steps" must be a non-empty array.');
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        $name = $data['name'] ?? null;
        if ($name !== null && !is_string($name)) {
            $response = $this->makeResponse(ResponseCodes::BAD_REQUEST, 'Field "name" must be a string when provided.');
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        $name = is_string($name) ? trim($name) : null;

        // Persist the steps array as JSON, following the same directory structure
        // as Coco\SourceWatcher\Core\Pipeline\SourceWatcher::save().
        $jsonRepresentation = json_encode($steps, JSON_PRETTY_PRINT);

        $transformationsDirectory = $this->getTransformationsDirectory();
        if ($transformationsDirectory === null) {
            $response = $this->makeResponse(ResponseCodes::INTERNAL_SERVER_ERROR, 'Unable to create transformations directory.');
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        if (empty($name)) {
            // Simple random name; avoids coupling to any specific UUID implementation.
            $name = 'transformation_' . bin2hex(random_bytes(8));
        }

        $filePath = $transformationsDirectory . DIRECTORY_SEPARATOR . $name . '.swt';

        if (@file_put_contents($filePath, $jsonRepresentation) === false) {
            $response = $this->makeResponse(ResponseCodes::INTERNAL_SERVER_ERROR, 'Unable to write transformation file.');
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            return;
        }

        $payload = [
            'name' => $name,
            'path' => $filePath,
        ];

        $response = $this->makeArrayResponse(ResponseCodes::OK, $payload);
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
}

