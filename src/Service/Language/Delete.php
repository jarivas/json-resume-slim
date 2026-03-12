<?php

namespace App\Service\Language;

class Delete extends Language
{


    public function execute(): array
    {
        $id = ($this->args['language_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
