<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class AwardTest extends TestCase
{


    public function test_award_requires_authentication(): void
    {
        $response = $this->post('/award', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_award_requires_authentication()


    public function test_award_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/award', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Best Engineer', $created['title']);

        $id = $created['id'];

        $list = $this->get('/award', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/award/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Best Engineer', $current['title']);

        $updated = $this->patch(
            "/award/$id",
            [
                'summary' => 'Updated summary',
                'date'    => '2025-08',
            ],
            $headers
        )['result'];

        $this->assertEquals('Updated summary', $updated['summary']);
        $this->assertStringStartsWith('2025-08-01', $updated['date']);

        $deleted = $this->delete("/award/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/award/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_award_crud_flow()


    public function test_award_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/award',
            [
                'title'   => 'Best Engineer',
                'date'    => 'not-a-date',
                'awarder' => 'Acme',
                'summary' => 'Great work',
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_award_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'title'   => 'Best Engineer',
            'date'    => '2025-01-15',
            'awarder' => 'Acme Corp',
            'summary' => 'For outstanding delivery',
        ];

    }//end getCreatePayload()


}//end class
