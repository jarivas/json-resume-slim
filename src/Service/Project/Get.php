<?php

namespace App\Service\Project;

use App\Model\Projects;

class Get extends Project
{


    public function execute(): array
    {
        $rows = Projects::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
