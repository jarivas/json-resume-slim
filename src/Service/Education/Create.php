<?php

namespace App\Service\Education;

use App\Model\Educations;
use Ulid\Ulid;

class Create extends Education
{


    public function validate(): bool
    {
        if (!is_array($this->data)) {
            return false;
        }

        return $this->validatePayload($this->data, true);

    }//end validate()


    public function execute(): array
    {
        $model = new Educations();
        $model->id = Ulid::generate()->__toString();
        $model->setData($this->normalizePayload(($this->data ?? [])));
        $model->insert();
        $model->hydrate();

        return $this->parseModel($model);

    }//end execute()


}//end class
