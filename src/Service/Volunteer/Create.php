<?php

namespace App\Service\Volunteer;

use App\Model\Volunteers;
use Ulid\Ulid;

class Create extends Volunteer
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
        $model = new Volunteers();
        $model->id = Ulid::generate()->__toString();
        $model->setData($this->normalizePayload(($this->data ?? [])));
        $model->insert();
        $model->hydrate();

        return $this->parseModel($model);

    }//end execute()


}//end class
