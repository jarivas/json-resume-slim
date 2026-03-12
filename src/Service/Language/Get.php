<?php

namespace App\Service\Language;

use App\Model\Languages;

class Get extends Language
{


    public function execute(): array
    {
        $rows = Languages::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
