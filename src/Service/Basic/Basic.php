<?php

namespace App\Service\Basic;

use App\Model\Basics;
use App\Service\Service;
use Slim\Exception\HttpNotFoundException;

abstract class Basic extends Service
{

    /**
     * @var array<int, string>
     */
    protected array $allowedFields = [
        'name',
        'label',
        'email',
        'phone',
        'url',
        'summary',
        'location',
        'profiles',
    ];


    /**
     * @param array<string, mixed> $data
     */
    protected function validatePayload(array $data, bool $required=false): bool
    {
        if ($required && !$this->hasRequiredNonEmptyStringFields($data, ['name', 'label', 'email', 'phone'])) {
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
            'email' => $this->isValidEmail($value),
            'url' => $this->isNullableValidUrl($value),
            'location', 'profiles' => $value === null || is_array($value),
            default => $this->isNullableString($value),
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

            if ($field === 'location' || $field === 'profiles') {
                $result[$field] = ($value === null) ? null : json_encode($value, JSON_THROW_ON_ERROR);
                continue;
            }

            $result[$field] = $value;
        }

        return $result;

    }//end normalizePayload()


    protected function getModelById(string $id): Basics
    {
        $model = Basics::first(
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
     * @param array<int, Basics>|bool $rows
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
    protected function parseModel(Basics $model): array
    {
        $data = $model->toArray();

        foreach (['location', 'profiles'] as $field) {
            if (!isset($data[$field]) || !is_string($data[$field])) {
                continue;
            }

            $decoded = json_decode($data[$field], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$field] = $decoded;
            }
        }

        return $data;

    }//end parseModel()


}//end class
