<?php

namespace App\Service\Project;

class GetById extends Project
{


    public function execute(): array
    {
        $id = ($this->args['project_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
