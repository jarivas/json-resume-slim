<?php

namespace App\Service\Skill;

class Delete extends Skill
{


    public function execute(): array
    {
        $id = ($this->args['skill_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
