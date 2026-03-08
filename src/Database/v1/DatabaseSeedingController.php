<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Database\v1;

use Coco\SourceWatcherApi\Database\DatabaseMigrator;
use Coco\SourceWatcherApi\Framework\ApiResponse;
use Coco\SourceWatcherApi\Framework\Controller;
use Coco\SourceWatcherApi\Framework\ResponseCodes;
use DbConnectionSeeder;
use DbConnectionTypeSeeder;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DatabaseSeedingController extends Controller
{
    use ApiResponse;

    private Logger $log;

    public function __construct()
    {
        $logPath = join('/', [__DIR__, '..', '..', '..', 'logs', time() . '.log']);

        $this->log = new Logger(DatabaseSeedingController::class);
        $this->log->pushHandler(new StreamHandler($logPath, Logger::INFO));

        parent::__construct();
    }

    public function processRequest(string $requestMethod, array $extraOptions): void
    {
        if ($requestMethod == 'POST') {
            $response = $this->seedTable();
        } else {
            $response = $this->notFoundResponse();
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function getName(string $className): string
    {
        $path = explode('\\', $className);
        return array_pop($path);
    }

    private function seedTable(): array
    {
        $table = $this->requestData['table'];

        if (empty($table)) {
            return $this->makeResponse(ResponseCodes::BAD_REQUEST, 'Missing table');
        }

        $seed = null;

        switch ($table) {
            case 'db_connection':
                $seed = self::getName(DbConnectionSeeder::class);
                break;
            case 'db_connection_type':
                $seed = self::getName(DbConnectionTypeSeeder::class);
                break;
            case 'user':
                $seed = self::getName(UserSeeder::class);
                break;
        }

        if (empty($seed)) {
            return $this->makeResponse(ResponseCodes::BAD_REQUEST, 'Wrong seed');
        }

        $apiRoot = join('/', [__DIR__, '..', '..', '..']);
        if (is_file($apiRoot . '/.env')) {
            Dotenv::createImmutable($apiRoot)->load();
        }

        $databaseMigrator = new DatabaseMigrator();
        $databaseMigrator->seedDatabase($_ENV['DB_NAME'], $seed);

        return $this->makeResponse(ResponseCodes::OK);
    }
}
