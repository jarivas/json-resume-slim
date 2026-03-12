<?php

namespace App\Service\Work;

use App\Model\Works;

class Get extends Work
{


    public function execute(): array
    {
        $rows = Works::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
