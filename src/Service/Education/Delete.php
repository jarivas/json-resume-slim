<?php

namespace App\Service\Education;

class Delete extends Education
{


    public function execute(): array
    {
        $id = ($this->args['education_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
