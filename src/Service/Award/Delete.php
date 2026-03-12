<?php

namespace App\Service\Award;

class Delete extends Award
{


    public function execute(): array
    {
        $id = ($this->args['award_id'] ?? '');
        $model = $this->getModelById($id);

        return $model->delete() ? ['message' => 'Deleted successfully'] : ['message' => 'Delete failed'];

    }//end execute()


}//end class
