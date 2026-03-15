<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class PublicationTest extends TestCase
{


    public function test_publication_requires_authentication(): void
    {
        $response = $this->post('/publication', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_publication_requires_authentication()


    public function test_publication_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/publication', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Web Performance Guide', $created['name']);

        $id = $created['id'];

        $list = $this->get('/publication', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/publication/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('IEEE', $current['publisher']);

        $updated = $this->patch(
            "/publication/$id",
            [
                'summary'     => 'Updated summary',
                'releaseDate' => '2024-11',
            ],
            $headers
        )['result'];

        $this->assertEquals('Updated summary', $updated['summary']);
        $this->assertStringStartsWith('2024-11-01', $updated['releaseDate']);

        $deleted = $this->delete("/publication/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/publication/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_publication_crud_flow()


    public function test_publication_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/publication',
            [
                'name'        => 'Web Performance Guide',
                'publisher'   => 'IEEE',
                'releaseDate' => 'invalid-date',
                'summary'     => 'Publication summary',
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_publication_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'        => 'Web Performance Guide',
            'publisher'   => 'IEEE',
            'releaseDate' => '2024-10-01',
            'url'         => 'https://example.com/publication',
            'summary'     => 'Publication summary',
        ];

    }//end getCreatePayload()


}//end class
