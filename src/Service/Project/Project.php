<?php

namespace App\Service\Project;

use App\Model\Projects;
use App\Service\Service;
use Slim\Exception\HttpNotFoundException;

abstract class Project extends Service
{

    /**
     * @var array<int, string>
     */
    protected array $allowedFields = [
        'name',
        'startDate',
        'endDate',
        'description',
        'highlights',
        'url',
        'basic_id',
    ];


    /**
     * @param array<string, mixed> $data
     */
    protected function validatePayload(array $data, bool $required=false): bool
    {
        if ($required && !$this->hasRequiredPresentFields($data, ['name', 'startDate', 'endDate', 'description', 'highlights'])) {
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
            'startDate', 'endDate' => is_string($value) && $this->normalizeIsoDate($value) !== null,
            'highlights' => $this->isArrayOfStrings($value),
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

            if (($field === 'startDate' || $field === 'endDate') && is_string($value)) {
                $normalizedDate = $this->normalizeIsoDate($value);
                if ($normalizedDate !== null) {
                    $result[$field] = $normalizedDate;
                }

                continue;
            }

            if ($field === 'highlights') {
                $result[$field] = json_encode($value, JSON_THROW_ON_ERROR);
                continue;
            }

            $result[$field] = $value;
        }//end foreach

        return $result;

    }//end normalizePayload()


    protected function getModelById(string $id): Projects
    {
        $model = Projects::first(
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
     * @param array<int, Projects>|bool $rows
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
    protected function parseModel(Projects $model): array
    {
        $data = $model->toArray();

        if (isset($data['highlights']) && is_string($data['highlights'])) {
            $decoded = json_decode($data['highlights'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['highlights'] = $decoded;
            }
        }

        return $data;

    }//end parseModel()


}//end class
