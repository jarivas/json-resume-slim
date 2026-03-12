<?php

namespace App\Service\Volunteer;

class Delete extends Volunteer
{


    public function execute(): array
    {
        $id = ($this->args['volunteer_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
