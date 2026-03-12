<?php

namespace App\Service\Certificate;

use App\Model\Certificates;

class Get extends Certificate
{


    public function execute(): array
    {
        $rows = Certificates::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
