<?php

namespace App\Service\Skill;

use App\Model\Skills;

class Get extends Skill
{


    public function execute(): array
    {
        $rows = Skills::get([], 0, 100);

        return [
            'items' => $this->parseRows($rows),
        ];

    }//end execute()


}//end class
