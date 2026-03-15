<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class WorkTest extends TestCase
{


    public function test_work_requires_authentication(): void
    {
        $response = $this->post('/work', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_work_requires_authentication()


    public function test_work_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/work', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Acme Corp', $created['name']);
        $this->assertIsArray($created['highlights']);

        $id = $created['id'];

        $list = $this->get('/work', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/work/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Backend Engineer', $current['position']);

        $updated = $this->patch(
            "/work/$id",
            [
                'summary'    => 'Updated summary',
                'endDate'    => '2025-09',
                'highlights' => [
                    'api',
                    'scalability',
                ],
            ],
            $headers
        )['result'];

        $this->assertEquals('Updated summary', $updated['summary']);
        $this->assertStringStartsWith('2025-09-01', $updated['endDate']);
        $this->assertIsArray($updated['highlights']);

        $deleted = $this->delete("/work/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/work/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_work_crud_flow()


    public function test_work_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/work',
            [
                'name'       => 'Acme Corp',
                'position'   => 'Backend Engineer',
                'startDate'  => 'invalid-date',
                'endDate'    => '2025-12-31',
                'summary'    => 'Built APIs',
                'highlights' => ['api'],
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_work_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'       => 'Acme Corp',
            'position'   => 'Backend Engineer',
            'url'        => 'https://example.com',
            'startDate'  => '2024-01-01',
            'endDate'    => '2025-08-31',
            'summary'    => 'Built APIs',
            'highlights' => [
                'api',
                'performance',
            ],
        ];

    }//end getCreatePayload()


}//end class
