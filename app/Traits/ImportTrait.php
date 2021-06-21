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
        $uniqueBy = isset($rows[$this->uniqueBy()]) && !$errors->get($this->uniqueBy());
        if ($uniqueBy) {
            $this->checkDuplicateSampel($key, $rows);
        }
    }

    public function setData(array $data)
    {
        array_push($this->result['data'], $data);
    }

    public function getStatusCodeResponse()
    {
        return $this->result['errors_count'] > 0 ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;
    }

    public function checkValidLimit($rows)
    {
        abort_if(
            count($rows) >= $this->limit,
            Response::HTTP_BAD_REQUEST,
            __('validation.excel_data_limit', ['limit' => $this->limit])
        );
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

    public function initError($key)
    {
        $this->result['errors'][$key] = null;
    }

    public function checkDuplicateSampel($key, $rows)
    {
        $uniqueBy = $this->getUniqueBy($rows);

        if (!$uniqueBy) {
            return;
        }

        $uniqueBy = strtoupper($uniqueBy);

        if (in_array($uniqueBy, $this->uniqueBy)) {
            $this->setError($key, __('validation.unique', ['attribute' => $this->uniqueBy()]));
        } else {
            $this->sampel[] = $uniqueBy;
        }
    }

    public function getUniqueBy($rows)
    {
        return $rows[$this->uniqueBy()] ?? null;
    }

    public function setMessage($message)
    {
        $this->result['message'] = $message;
    }

    public function getItemsValidated(array $rows, bool $isArray = true)
    {
        $keyRules = array_keys($this->rules());

        $items = collect($rows)->only($keyRules);

        return $isArray ? $items->toArray() : $items;
    }

    public function setUp($key)
    {
        $this->initError($key);
        $this->setNumberRow($key);
    }

    public function setNumberRow($key)
    {
        $this->result['number_row'][] = $key + 1;
    }
}
