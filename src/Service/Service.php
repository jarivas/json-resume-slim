<?php

namespace App\Service;

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


}//end class
