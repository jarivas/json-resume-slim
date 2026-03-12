<?php

namespace App\Service\Publication;

class Delete extends Publication
{


    public function execute(): array
    {
        $id = ($this->args['publication_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
