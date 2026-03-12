<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class InterestTest extends TestCase
{


    public function test_interest_requires_authentication(): void
    {
        $response = $this->post('/interest', $this->getCreatePayload());

        $this->assertArrayHasKey('error', $response['result']);
        $this->assertEquals('Unauthorized', $response['result']['error']);

    }//end test_interest_requires_authentication()


    public function test_interest_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/interest', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Open Source', $created['name']);
        $this->assertIsArray($created['keywords']);

        $id = $created['id'];

        $list = $this->get('/interest', $headers)['result'];
        $this->assertArrayHasKey('items', $list);
        $this->assertIsArray($list['items']);

        $current = $this->get("/interest/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Open Source', $current['name']);

        $updated = $this->patch(
            "/interest/$id",
            [
                'keywords' => [
                    'linux',
                    'community',
                ],
            ],
            $headers
        )['result'];

        $this->assertIsArray($updated['keywords']);
        $this->assertEquals('linux', $updated['keywords'][0]);

        $deleted = $this->delete("/interest/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $deleted);
        $this->assertEquals('Deleted successfully', $deleted['message']);

        $notFound = $this->get("/interest/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $notFound);
        $this->assertEquals('404 Not Found', $notFound['message']);

    }//end test_interest_crud_flow()


    public function test_interest_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/interest',
            [
                'name'     => 'Open Source',
                'keywords' => 'not-an-array',
            ],
            $headers
        )['result'];

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('404 Not Found', $response['message']);

    }//end test_interest_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'     => 'Open Source',
            'keywords' => [
                'php',
                'api',
            ],
        ];

    }//end getCreatePayload()


}//end class
