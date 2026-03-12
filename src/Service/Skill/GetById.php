<?php

namespace App\Service\Skill;

class GetById extends Skill
{


    public function execute(): array
    {
        $id = ($this->args['skill_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
