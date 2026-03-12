<?php

namespace App\Service\Award;

class Update extends Award
{


    public function validate(): bool
    {
        if (!is_array($this->data) || empty($this->data)) {
            return false;
        }

        return $this->validatePayload($this->data, false);

    }//end validate()


    public function execute(): array
    {
        $id = ($this->args['award_id'] ?? '');
        $model = $this->getModelById($id);

        $model->setData($this->normalizePayload(($this->data ?? [])));
        $model->save();
        $model->hydrate();

        return $this->parseModel($model);

    }//end execute()


}//end class
