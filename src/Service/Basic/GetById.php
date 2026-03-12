<?php

namespace App\Service\Basic;

class GetById extends Basic
{


    public function execute(): array
    {
        $id = ($this->args['basic_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
