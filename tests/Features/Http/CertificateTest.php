<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class CertificateTest extends TestCase
{


    public function test_certificate_requires_authentication(): void
    {
        $response = $this->post('/certificate', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_certificate_requires_authentication()


    public function test_certificate_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/certificate', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('Kubernetes Admin', $created['name']);

        $id = $created['id'];

        $list = $this->get('/certificate', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/certificate/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Kubernetes Admin', $current['name']);

        $updated = $this->patch(
            "/certificate/$id",
            [
                'issuer' => 'The Linux Foundation',
                'date'   => '2025',
            ],
            $headers
        )['result'];

        $this->assertEquals('The Linux Foundation', $updated['issuer']);
        $this->assertStringStartsWith('2025-01-01', $updated['date']);

        $deleted = $this->delete("/certificate/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/certificate/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_certificate_crud_flow()


    public function test_certificate_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/certificate',
            [
                'name'   => 'Kubernetes Admin',
                'date'   => '2025-02-02',
                'issuer' => 'CNCF',
                'url'    => 'invalid-url',
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_certificate_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'   => 'Kubernetes Admin',
            'date'   => '2025-02-02',
            'issuer' => 'CNCF',
            'url'    => 'https://www.cncf.io/certification/cka/',
        ];

    }//end getCreatePayload()


}//end class
