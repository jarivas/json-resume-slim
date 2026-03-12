<?php

namespace App\Service\Publication;

class GetById extends Publication
{


    public function execute(): array
    {
        $id = ($this->args['publication_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
