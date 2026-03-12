<?php

namespace App\Service\Volunteer;

class GetById extends Volunteer
{


    public function execute(): array
    {
        $id = ($this->args['volunteer_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
