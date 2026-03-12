<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class LanguageTest extends TestCase
{


    public function test_language_requires_authentication(): void
    {
        $response = $this->post('/language', $this->getCreatePayload());

        $this->assertArrayHasKey('error', $response['result']);
        $this->assertEquals('Unauthorized', $response['result']['error']);

    }//end test_language_requires_authentication()


    public function test_language_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/language', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Spanish', $created['language']);

        $id = $created['id'];

        $list = $this->get('/language', $headers)['result'];
        $this->assertArrayHasKey('items', $list);
        $this->assertIsArray($list['items']);

        $current = $this->get("/language/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Native', $current['fluency']);

        $updated = $this->patch(
            "/language/$id",
            ['fluency' => 'Professional Working'],
            $headers
        )['result'];

        $this->assertEquals('Professional Working', $updated['fluency']);

        $deleted = $this->delete("/language/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $deleted);
        $this->assertEquals('Deleted successfully', $deleted['message']);

        $notFound = $this->get("/language/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $notFound);
        $this->assertEquals('404 Not Found', $notFound['message']);

    }//end test_language_crud_flow()


    public function test_language_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/language',
            [
                'language' => 'Spanish',
                'fluency'  => '',
            ],
            $headers
        )['result'];

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('404 Not Found', $response['message']);

    }//end test_language_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'language' => 'Spanish',
            'fluency'  => 'Native',
        ];

    }//end getCreatePayload()


}//end class
