<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Pipeline;

/**
 * List of available pipeline steps (extractors, transformers, loaders) for the board.
 * Keep in sync with source-watcher-core when adding new steps.
 */
final class StepsConfig
{
    public const TYPE_EXTRACTOR = 'extractor';
    public const TYPE_EXECUTION_EXTRACTOR = 'execution-extractor';
    public const TYPE_TRANSFORMER = 'transformer';
    public const TYPE_LOADER = 'loader';

    public static function getSteps(): array
    {
        return [
            ['id' => 'extractor-1', 'type' => self::TYPE_EXTRACTOR, 'name' => 'CSV', 'object' => 'CsvExtractor'],
            ['id' => 'extractor-2', 'type' => self::TYPE_EXTRACTOR, 'name' => 'Database', 'object' => 'DatabaseExtractor'],
            ['id' => 'extractor-3', 'type' => self::TYPE_EXECUTION_EXTRACTOR, 'name' => 'Find Missing From Sequence', 'object' => 'FindMissingFromSequenceExtractor'],
            ['id' => 'extractor-4', 'type' => self::TYPE_EXTRACTOR, 'name' => 'JSON', 'object' => 'JsonExtractor'],
            ['id' => 'extractor-5', 'type' => self::TYPE_EXTRACTOR, 'name' => 'TXT', 'object' => 'TxtExtractor'],
            ['id' => 'transformer-1', 'type' => self::TYPE_TRANSFORMER, 'name' => 'Convert Case', 'object' => 'ConvertCaseTransformer'],
            ['id' => 'transformer-2', 'type' => self::TYPE_TRANSFORMER, 'name' => 'Guess Gender', 'object' => 'GuessGenderTransformer'],
            ['id' => 'transformer-3', 'type' => self::TYPE_TRANSFORMER, 'name' => 'Java', 'object' => 'JavaTransformer'],
            ['id' => 'transformer-4', 'type' => self::TYPE_TRANSFORMER, 'name' => 'Rename Columns', 'object' => 'RenameColumnsTransformer'],
            ['id' => 'loader-1', 'type' => self::TYPE_LOADER, 'name' => 'Database', 'object' => 'DatabaseLoader'],
        ];
    }
}
