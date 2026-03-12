<?php

namespace App\Service;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Service
{

    /**
     * Summary of data
     * @var array<string, mixed>|null
     */
    protected null|array $data = null;


    /**
     * Summary of __construct
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string, mixed> $args
     */
    public function __construct(
        protected ServerRequestInterface $request,
        protected ResponseInterface $response,
        protected array $args
    ) {
        $this->setData();

    }//end __construct()


    public function validate(): bool
    {
        // Default validation logic can be implemented here.
        // Child classes can override this method to provide specific validation.
        return true;

    }//end validate()


    /**
     * Summary of execute
     * @return array<string, mixed>
     */
    abstract public function execute(): array;


    protected function setData(): void
    {
        $tmp = $this->request->getParsedBody();
        if (is_object($tmp)) {
            $this->data = (array) $tmp;
            return;
        }

        if (is_array($tmp)) {
            $this->data = $tmp;
            return;
        }

        $rawBody = (string) $this->request->getBody();
        if ($rawBody === '') {
            return;
        }

        $decodedBody = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedBody)) {
            return;
        }

        $this->data = $decodedBody;

    }//end setData()


    /**
     * @param array<string, mixed> $data
     * @param array<int, string>   $fields
     */
    protected function hasRequiredPresentFields(array $data, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                return false;
            }
        }

        return true;

    }//end hasRequiredPresentFields()


    /**
     * @param array<string, mixed> $data
     * @param array<int, string>   $fields
     */
    protected function hasRequiredNonEmptyStringFields(array $data, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || !$this->isNonEmptyString($data[$field])) {
                return false;
            }
        }

        return true;

    }//end hasRequiredNonEmptyStringFields()


    /**
     * @param array<int, string> $allowedFields
     */
    protected function isAllowedField(string $field, array $allowedFields): bool
    {
        return in_array($field, $allowedFields, true);

    }//end isAllowedField()


    protected function isNonEmptyString(mixed $value): bool
    {
        return is_string($value) && $value !== '';

    }//end isNonEmptyString()


    protected function isNullableNonEmptyString(mixed $value): bool
    {
        return $value === null || $this->isNonEmptyString($value);

    }//end isNullableNonEmptyString()


    protected function isNullableString(mixed $value): bool
    {
        return $value === null || is_string($value);

    }//end isNullableString()


    protected function isValidEmail(mixed $value): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

    }//end isValidEmail()


    protected function isNullableValidUrl(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false;

    }//end isNullableValidUrl()


    protected function isArrayOfStrings(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!is_string($item)) {
                return false;
            }
        }

        return true;

    }//end isArrayOfStrings()


    protected function isNullableArrayOfStrings(mixed $value): bool
    {
        return $value === null || $this->isArrayOfStrings($value);

    }//end isNullableArrayOfStrings()


    protected function normalizeIsoDate(string $value): ?string
    {
        $value = trim($value);

        if (preg_match('/^\d{4}$/', $value) === 1) {
            return "$value-01-01 00:00:00";
        }

        if (preg_match('/^\d{4}-\d{2}$/', $value) === 1) {
            return "$value-01 00:00:00";
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return "$value 00:00:00";
        }

        if (strtotime($value) === false) {
            return null;
        }

        return (new DateTime($value))->format('Y-m-d H:i:s');

    }//end normalizeIsoDate()


}//end class
