<?php

namespace App\Service\Interest;

class GetById extends Interest
{


    public function execute(): array
    {
        $id = ($this->args['interest_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
