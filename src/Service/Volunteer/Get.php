<?php

namespace App\Service\Volunteer;

use App\Model\Volunteers;

class Get extends Volunteer
{


    public function execute(): array
    {
        $rows = Volunteers::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
