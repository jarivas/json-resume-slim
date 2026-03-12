<?php

namespace App\Service\Publication;

class Update extends Publication
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
        $id = ($this->args['publication_id'] ?? '');
        $model = $this->getModelById($id);

        $model->setData($this->normalizePayload(($this->data ?? [])));
        $model->save();
        $model->hydrate();

        return $this->parseModel($model);

    }//end execute()


}//end class
