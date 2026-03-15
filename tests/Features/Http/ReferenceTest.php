<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class ReferenceTest extends TestCase
{


    public function test_reference_requires_authentication(): void
    {
        $response = $this->post('/reference', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_reference_requires_authentication()


    public function test_reference_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/reference', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Jane Doe', $created['name']);

        $id = $created['id'];

        $list = $this->get('/reference', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/reference/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Jane Doe', $current['name']);

        $updated = $this->patch(
            "/reference/$id",
            ['reference' => 'Updated recommendation text'],
            $headers
        )['result'];

        $this->assertEquals('Updated recommendation text', $updated['reference']);

        $deleted = $this->delete("/reference/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/reference/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_reference_crud_flow()


    public function test_reference_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/reference',
            [
                'name'      => 'Jane Doe',
                'reference' => '',
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_reference_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'      => 'Jane Doe',
            'reference' => 'Great engineer and team player',
        ];

    }//end getCreatePayload()


}//end class
