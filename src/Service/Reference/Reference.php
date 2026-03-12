<?php

namespace App\Service\Reference;

use App\Model\References;
use App\Service\Service;
use Slim\Exception\HttpNotFoundException;

abstract class Reference extends Service
{

    /**
     * @var array<int, string>
     */
    protected array $allowedFields = [
        'name',
        'reference',
        'basic_id',
    ];


    /**
     * @param array<string, mixed> $data
     */
    protected function validatePayload(array $data, bool $required=false): bool
    {
        if ($required && !$this->hasRequiredNonEmptyStringFields($data, ['name', 'reference'])) {
            return false;
        }

        foreach ($data as $field => $value) {
            if (!$this->isAllowedField($field, $this->allowedFields) || !$this->isValidFieldValue($field, $value)) {
                return false;
            }
        }

        return true;

    }//end validatePayload()


    protected function isValidFieldValue(string $field, mixed $value): bool
    {
        return match ($field) {
            'basic_id' => $this->isNullableNonEmptyString($value),
            default => $this->isNonEmptyString($value),
        };

    }//end isValidFieldValue()


    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function normalizePayload(array $data): array
    {
        $result = [];

        foreach ($data as $field => $value) {
            if (!in_array($field, $this->allowedFields, true)) {
                continue;
            }

            $result[$field] = $value;
        }

        return $result;

    }//end normalizePayload()


    protected function getModelById(string $id): References
    {
        $model = References::first(
            [
                [
                    'id',
                    '=',
                    $id,
                ],
            ]
        );

        if (is_bool($model)) {
            throw new HttpNotFoundException($this->request);
        }

        return $model;

    }//end getModelById()


    /**
     * @param array<int, References>|bool $rows
     * @return array<int, array<string, mixed>>
     */
    protected function parseRows(array|bool $rows): array
    {
        if (is_bool($rows)) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->parseModel($row);
        }

        return $result;

    }//end parseRows()


    /**
     * @return array<string, mixed>
     */
    protected function parseModel(References $model): array
    {
        return $model->toArray();

    }//end parseModel()


}//end class
