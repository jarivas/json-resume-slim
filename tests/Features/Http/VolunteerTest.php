<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class VolunteerTest extends TestCase
{


    public function test_volunteer_requires_authentication(): void
    {
        $response = $this->post('/volunteer', $this->getCreatePayload());

        $this->assertArrayHasKey('error', $response['result']);
        $this->assertEquals('Unauthorized', $response['result']['error']);

    }//end test_volunteer_requires_authentication()


    public function test_volunteer_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/volunteer', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Open Source Org', $created['organization']);
        $this->assertIsArray($created['highlights']);

        $id = $created['id'];

        $list = $this->get('/volunteer', $headers)['result'];
        $this->assertArrayHasKey('items', $list);
        $this->assertIsArray($list['items']);

        $current = $this->get("/volunteer/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Mentor', $current['position']);

        $updated = $this->patch(
            "/volunteer/$id",
            [
                'summary'    => 'Updated summary',
                'endDate'    => '2025-09',
                'highlights' => [
                    'mentoring',
                    'events',
                ],
            ],
            $headers
        )['result'];

        $this->assertEquals('Updated summary', $updated['summary']);
        $this->assertStringStartsWith('2025-09-01', $updated['endDate']);
        $this->assertIsArray($updated['highlights']);

        $deleted = $this->delete("/volunteer/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $deleted);
        $this->assertEquals('Deleted successfully', $deleted['message']);

        $notFound = $this->get("/volunteer/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $notFound);
        $this->assertEquals('404 Not Found', $notFound['message']);

    }//end test_volunteer_crud_flow()


    public function test_volunteer_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/volunteer',
            [
                'organization' => 'Open Source Org',
                'position'     => 'Mentor',
                'startDate'    => 'invalid-date',
                'endDate'      => '2025-12-31',
                'summary'      => 'Community support',
                'highlights'   => ['mentoring'],
            ],
            $headers
        )['result'];

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('404 Not Found', $response['message']);

    }//end test_volunteer_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'organization' => 'Open Source Org',
            'position'     => 'Mentor',
            'url'          => 'https://example.org',
            'startDate'    => '2024-01-01',
            'endDate'      => '2025-08-31',
            'summary'      => 'Community support',
            'highlights'   => [
                'mentoring',
                'talks',
            ],
        ];

    }//end getCreatePayload()


}//end class
