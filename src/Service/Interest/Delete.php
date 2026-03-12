<?php

namespace App\Service\Interest;

class Delete extends Interest
{


    public function execute(): array
    {
        $id = ($this->args['interest_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
