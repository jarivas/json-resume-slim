<?php

namespace App\Service\Award;

use App\Model\Awards;

class Get extends Award
{


    public function execute(): array
    {
        $rows = Awards::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
