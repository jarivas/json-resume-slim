<?php

namespace App\Service\Certificate;

class GetById extends Certificate
{


    public function execute(): array
    {
        $id = ($this->args['certificate_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
