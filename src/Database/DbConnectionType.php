<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Database;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class DbConnectionType implements JsonSerializable
{
    private int $id;
    private string $driver;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): void
    {
        $this->driver = $driver;
    }

    #[ArrayShape(['id' => "int", 'driver' => "string"])]
    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'driver' => $this->driver];
    }
}
