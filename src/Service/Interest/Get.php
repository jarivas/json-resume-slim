<?php

namespace App\Service\Interest;

use App\Model\Interests;

class Get extends Interest
{


    public function execute(): array
    {
        $rows = Interests::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
