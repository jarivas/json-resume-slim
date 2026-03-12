<?php

namespace App\Service\Basic;

use App\Model\Basics;

class Get extends Basic
{


    public function execute(): array
    {
        $rows = Basics::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
