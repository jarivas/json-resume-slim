<?php

namespace App\Service\Work;

class GetById extends Work
{


    public function execute(): array
    {
        $id = ($this->args['work_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
