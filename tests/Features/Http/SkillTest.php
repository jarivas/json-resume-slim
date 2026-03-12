<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class SkillTest extends TestCase
{


    public function test_skill_requires_authentication(): void
    {
        $response = $this->post('/skill', $this->getCreatePayload());

        $this->assertArrayHasKey('error', $response['result']);
        $this->assertEquals('Unauthorized', $response['result']['error']);

    }//end test_skill_requires_authentication()


    public function test_skill_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/skill', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('PHP', $created['name']);
        $this->assertIsArray($created['keywords']);

        $id = $created['id'];

        $list = $this->get('/skill', $headers)['result'];
        $this->assertArrayHasKey('items', $list);
        $this->assertIsArray($list['items']);

        $current = $this->get("/skill/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Advanced', $current['level']);

        $updated = $this->patch(
            "/skill/$id",
            [
                'keywords' => [
                    'phpunit',
                    'slim',
                ],
            ],
            $headers
        )['result'];

        $this->assertIsArray($updated['keywords']);
        $this->assertEquals('phpunit', $updated['keywords'][0]);

        $deleted = $this->delete("/skill/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $deleted);
        $this->assertEquals('Deleted successfully', $deleted['message']);

        $notFound = $this->get("/skill/$id", $headers)['result'];
        $this->assertArrayHasKey('message', $notFound);
        $this->assertEquals('404 Not Found', $notFound['message']);

    }//end test_skill_crud_flow()


    public function test_skill_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/skill',
            [
                'name'     => 'PHP',
                'level'    => 'Advanced',
                'keywords' => 'not-array',
            ],
            $headers
        )['result'];

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('404 Not Found', $response['message']);

    }//end test_skill_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'name'     => 'PHP',
            'level'    => 'Advanced',
            'keywords' => [
                'slim',
                'api',
            ],
        ];

    }//end getCreatePayload()


}//end class
