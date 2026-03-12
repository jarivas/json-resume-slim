<?php

namespace App\Service\Publication;

use App\Model\Publications;
use App\Service\Service;
use Slim\Exception\HttpNotFoundException;

abstract class Publication extends Service
{

    /**
     * @var array<int, string>
     */
    protected array $allowedFields = [
        'name',
        'publisher',
        'releaseDate',
        'url',
        'summary',
        'basic_id',
    ];


    /**
     * @param array<string, mixed> $data
     */
    protected function validatePayload(array $data, bool $required=false): bool
    {
        if ($required && !$this->hasRequiredNonEmptyStringFields($data, ['name', 'publisher', 'releaseDate', 'summary'])) {
            return false;
        }

        foreach ($data as $field => $value) {
            if (!$this->isAllowedField($field, $this->allowedFields) || !$this->isValidFieldValue($field, $value)) {
                return false;
            }
        }//end foreach

        return true;

    }//end validatePayload()


    protected function isValidFieldValue(string $field, mixed $value): bool
    {
        return match ($field) {
            'releaseDate' => is_string($value) && $this->normalizeIsoDate($value) !== null,
            'url' => $this->isNullableValidUrl($value),
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
            if (!$this->isAllowedField($field, $this->allowedFields)) {
                continue;
            }

            if ($field === 'releaseDate' && is_string($value)) {
                $normalizedDate = $this->normalizeIsoDate($value);
                if ($normalizedDate !== null) {
                    $result[$field] = $normalizedDate;
                }

                continue;
            }

            $result[$field] = $value;
        }

        return $result;

    }//end normalizePayload()


    protected function getModelById(string $id): Publications
    {
        $model = Publications::first(
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
     * @param array<int, Publications>|bool $rows
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
    protected function parseModel(Publications $model): array
    {
        return $model->toArray();

    }//end parseModel()


}//end class
