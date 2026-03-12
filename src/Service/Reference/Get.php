<?php

namespace App\Service\Reference;

use App\Model\References;

class Get extends Reference
{


    public function execute(): array
    {
        $rows = References::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
