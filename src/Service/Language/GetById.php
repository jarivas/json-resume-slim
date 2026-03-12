<?php

namespace App\Service\Language;

class GetById extends Language
{


    public function execute(): array
    {
        $id = ($this->args['language_id'] ?? '');
        $model = $this->getModelById($id);

        return $this->parseModel($model);

    }//end execute()


}//end class
