<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

trait ImportTrait
{
    private $limit = 200;

    public $result = [
        'message' => 'Sukses membaca file import excel',
        'data' => [],
        'errors' => [],
        'errors_count' => 0,
        'number_row' => []
    ];

    private $uniqueBy = [];

    public function validated(array $rows, $key)
    {
        App::setLocale('id');
        $validator = Validator::make($rows, $this->rules());
        $this->setUp($key);
        if ($validator->fails()) {
            $this->setError($key, $validator->errors()->all());
        }
        $errors = $validator->errors();
    }

    public function setData(array $data)
    {
        array_push($this->result['data'], $data);
    }

    public function getStatusCodeResponse()
    {
        return $this->result['errors_count'] > 0 ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;
    }

    public function setError($key, $message)
    {
        if (is_array($message)) {
            $this->result['errors'][$key] = $message;
        } else {
            $this->result['errors'][$key][] = $message;
        }
        ++$this->result['errors_count'];
    }

    public function setMessage($message)
    {
        $this->result['message'] = $message;
    }

    public function setUp($key)
    {
        $this->initError($key);
        $this->setNumberRow($key);
    }

    public function initError($key)
    {
        $this->result['errors'][$key] = null;
    }

    public function setNumberRow($key)
    {
        $this->result['number_row'][] = $key + 1;
    }
}
