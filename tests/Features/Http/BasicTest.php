<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class BasicTest extends TestCase
{


    public function test_basic_requires_authentication(): void
    {
        $response = $this->post('/basic', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_basic_requires_authentication()


    public function test_basic_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/basic', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertArrayHasKey('name', $created);
        $this->assertArrayHasKey('location', $created);
        $this->assertArrayHasKey('profiles', $created);

        $id = $created['id'];

        $list = $this->get('/basic', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/basic/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('John Doe', $current['name']);

        $updated = $this->patch(
            "/basic/$id",
            [
                'summary'  => 'Updated summary',
                'location' => ['city' => 'Barcelona'],
            ],
            $headers
        )['result'];

        $this->assertEquals('Updated summary', $updated['summary']);
        $this->assertIsArray($updated['location']);
        $this->assertEquals('Barcelona', $updated['location']['city']);

        $deleted = $this->delete("/basic/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/basic/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_basic_crud_flow()


    public function test_basic_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/basic',
            [
                'name'  => 'John Doe',
                'label' => 'Engineer',
                'email' => 'invalid-email',
                'phone' => '+34111222333',
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_basic_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'     => 'John Doe',
            'label'    => 'Software Engineer',
            'email'    => 'john.doe@example.com',
            'phone'    => '+34000000000',
            'url'      => 'https://example.com',
            'summary'  => 'Backend engineer',
            'location' => [
                'address'     => 'Street 123',
                'postalCode'  => '08001',
                'city'        => 'Madrid',
                'countryCode' => 'ES',
                'region'      => 'MD',
            ],
            'profiles' => [
                [
                    'network'  => 'github',
                    'username' => 'johndoe',
                    'url'      => 'https://github.com/johndoe',
                ],
            ],
        ];

    }//end getCreatePayload()


}//end class
