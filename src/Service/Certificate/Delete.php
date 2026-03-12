<?php

namespace App\Service\Certificate;

class Delete extends Certificate
{


    public function execute(): array
    {
        $id = ($this->args['certificate_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
