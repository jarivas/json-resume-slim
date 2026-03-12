<?php

namespace App\Service\Basic;

class Delete extends Basic
{


    public function execute(): array
    {
        $id = ($this->args['basic_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
