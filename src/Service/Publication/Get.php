<?php

namespace App\Service\Publication;

use App\Model\Publications;

class Get extends Publication
{


    public function execute(): array
    {
        $rows = Publications::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
