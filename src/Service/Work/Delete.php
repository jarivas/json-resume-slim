<?php

namespace App\Service\Work;

class Delete extends Work
{


    public function execute(): array
    {
        $id = ($this->args['work_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
