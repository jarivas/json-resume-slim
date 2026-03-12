<?php

namespace App\Service\Basic;

use App\Model\Basics;
use Ulid\Ulid;

class Create extends Basic
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
        $model = new Basics();
        $model->id = Ulid::generate()->__toString();
        $model->setData($this->normalizePayload(($this->data ?? [])));
        $model->insert();
        $model->hydrate();

        return $this->parseModel($model);

    }//end execute()


}//end class
