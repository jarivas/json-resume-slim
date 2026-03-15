<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class EducationTest extends TestCase
{


    public function test_education_requires_authentication(): void
    {
        $response = $this->post('/education', $this->getCreatePayload());

        $this->assertErrorContract($response['result'], 401, 'Unauthorized');

    }//end test_education_requires_authentication()


    public function test_education_crud_flow(): void
    {
        $headers = $this->getAuthHeaders();

        $created = $this->post('/education', $this->getCreatePayload(), $headers)['result'];
        $this->assertArrayHasKey('id', $created);
        $this->assertEquals('MIT', $created['institution']);
        $this->assertIsArray($created['courses']);

        $id = $created['id'];

        $list = $this->get('/education', $headers)['result'];
        $this->assertItemsCollection($list);

        $current = $this->get("/education/$id", $headers)['result'];
        $this->assertEquals($id, $current['id']);
        $this->assertEquals('Computer Science', $current['area']);

        $updated = $this->patch(
            "/education/$id",
            [
                'score'     => '3.9/4.0',
                'courses'   => [
                    'Distributed Systems',
                    'Compilers',
                ],
                'startDate' => '2020-09',
            ],
            $headers
        )['result'];

        $this->assertEquals('3.9/4.0', $updated['score']);
        $this->assertIsArray($updated['courses']);
        $this->assertStringStartsWith('2020-09-01', $updated['startDate']);

        $deleted = $this->delete("/education/$id", $headers)['result'];
        $this->assertDeletedSuccessfully($deleted);

        $notFound = $this->get("/education/$id", $headers)['result'];
        $this->assertErrorContract($notFound, 404, 'Not Found');

    }//end test_education_crud_flow()


    public function test_education_invalid_payload(): void
    {
        $headers = $this->getAuthHeaders();

        $response = $this->post(
            '/education',
            [
                'institution' => 'MIT',
                'area'        => 'Computer Science',
                'studyType'   => 'Bachelor',
                'startDate'   => 'invalid-date',
                'endDate'     => '2024-06-10',
                'summary'     => 'CS degree',
            ],
            $headers
        )['result'];

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_education_invalid_payload()


    private function getCreatePayload(): array
    {
        return [
            'institution' => 'MIT',
            'url'         => 'https://web.mit.edu',
            'area'        => 'Computer Science',
            'studyType'   => 'Bachelor',
            'startDate'   => '2020-09-01',
            'endDate'     => '2024-06-10',
            'score'       => '3.7/4.0',
            'summary'     => 'CS degree',
            'courses'     => [
                'Algorithms',
                'Operating Systems',
            ],
        ];

    }//end getCreatePayload()


}//end class
