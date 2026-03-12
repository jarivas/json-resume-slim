<?php

namespace App\Service\Award;

class GetById extends Award
{


    public function execute(): array
    {
        $id = ($this->args['award_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
