<?php

namespace App\Service\Interest;

use App\Model\Interests;
use App\Service\Service;
use Slim\Exception\HttpNotFoundException;

abstract class Interest extends Service
{

    /**
     * @var array<int, string>
     */
    protected array $allowedFields = [
        'name',
        'keywords',
        'basic_id',
    ];


    /**
     * @param array<string, mixed> $data
     */
    protected function validatePayload(array $data, bool $required=false): bool
    {
        if ($required) {
            if (empty($data['name']) || !$this->isNonEmptyString($data['name'])) {
                return false;
            }

            if (empty($data['keywords'])) {
                return false;
            }
        }

        foreach ($data as $field => $value) {
            if (!$this->validateField($field, $value)) {
                return false;
            }
        }

        return true;

    }//end validatePayload()


    /**
     * @param string $field
     * @param mixed $value
     */
    private function validateField(string $field, mixed $value): bool
    {
        if (!$this->isAllowedField($field, $this->allowedFields)) {
            return false;
        }

        return match ($field) {
            'keywords' => $this->isArrayOfStrings($value),
            'basic_id' => $this->isNullableNonEmptyString($value),
            default => $this->isNonEmptyString($value),
        };

    }//end validateField()


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

            if ($field === 'keywords') {
                $result[$field] = json_encode($value, JSON_THROW_ON_ERROR);
                continue;
            }

            $result[$field] = $value;
        }

        return $result;

    }//end normalizePayload()


    protected function getModelById(string $id): Interests
    {
        $model = Interests::first(
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
     * @param array<int, Interests>|bool $rows
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
    protected function parseModel(Interests $model): array
    {
        $data = $model->toArray();

        if (isset($data['keywords']) && is_string($data['keywords'])) {
            $decoded = json_decode($data['keywords'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['keywords'] = $decoded;
            }
        }

        return $data;

    }//end parseModel()


}//end class
