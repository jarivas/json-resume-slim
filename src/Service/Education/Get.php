<?php

namespace App\Service\Education;

use App\Model\Educations;

class Get extends Education
{


    public function execute(): array
    {
        $rows = Educations::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
