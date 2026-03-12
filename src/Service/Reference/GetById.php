<?php

namespace App\Service\Reference;

class GetById extends Reference
{


    public function execute(): array
    {
        $id = ($this->args['reference_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
