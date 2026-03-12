<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class ProjectTest extends TestCase
{


    public function test_project_requires_authentication(): void
    {
        $response = $this->post('/project', $this->getCreatePayload());

        $this->assertArrayHasKey('error', $response['result']);
        $this->assertEquals('Unauthorized', $response['result']['error']);

    }//end test_project_requires_authentication()


    public function test_project_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/project', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Resume API', $created['name']);
        $this->assertIsArray($created['highlights']);

        $id = $created['id'];

        $list = $this->get('/project', $headers)['result'];
        $this->assertArrayHasKey('items', $list);
        $this->assertIsArray($list['items']);

        $current = $this->get("/project/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Resume API', $current['name']);

        $updated = $this->patch(
            "/project/$id",
            [
                'description' => 'Updated description',
                'highlights'  => [
                    'Auth',
                    'CRUD',
                ],
                'endDate'     => '2025-09',
            ],
            $headers
        )['result'];

        $this->assertEquals('Updated description', $updated['description']);
        $this->assertIsArray($updated['highlights']);
        $this->assertStringStartsWith('2025-09-01', $updated['endDate']);

        $deleted = $this->delete("/project/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $deleted);
        $this->assertEquals('Deleted successfully', $deleted['message']);

        $notFound = $this->get("/project/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $notFound);
        $this->assertEquals('404 Not Found', $notFound['message']);

    }//end test_project_crud_flow()


    public function test_project_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/project',
            [
                'name'        => 'Resume API',
                'startDate'   => '2025-01-01',
                'endDate'     => 'invalid-date',
                'description' => 'Project desc',
                'highlights'  => ['Auth'],
            ],
            $headers
        )['result'];

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('404 Not Found', $response['message']);

    }//end test_project_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'        => 'Resume API',
            'startDate'   => '2025-01-01',
            'endDate'     => '2025-08-31',
            'description' => 'Project desc',
            'highlights'  => [
                'Auth',
                'Slim 4',
            ],
            'url'         => 'https://example.com/project',
        ];

    }//end getCreatePayload()


}//end class
