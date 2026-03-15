<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class LanguageTest extends TestCase
{


    public function test_language_requires_authentication(): void
    {
        $response = $this->post('/language', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_language_requires_authentication()


    public function test_language_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/language', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Spanish', $created['language']);

        $id = $created['id'];

        $list = $this->get('/language', $headers)['result'];
        $this->assertItemsCollection($list);

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
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/language/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

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

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_language_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'language' => 'Spanish',
            'fluency'  => 'Native',
        ];

    }//end getCreatePayload()


}//end class
