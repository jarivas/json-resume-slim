<?php

namespace App\Service\Education;

class GetById extends Education
{


    public function execute(): array
    {
        $id = ($this->args['education_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
