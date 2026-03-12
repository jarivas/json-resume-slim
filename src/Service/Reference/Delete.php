<?php

namespace App\Service\Reference;

class Delete extends Reference
{


    public function execute(): array
    {
        $id = ($this->args['reference_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
