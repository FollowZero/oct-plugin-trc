<?php
namespace Plus\Trc\Http\Requests;

use Input;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request as BaseRequest;
use RLuders\JWTAuth\Exceptions\JsonValidationException;

abstract class Request extends BaseRequest
{
    /**
     * Validate the request
     *
     * @return boolean|Response
     */
    public function validate()
    {
        $validator = Validator::make($this->data(), $this->rules(),$this->messages());
        if ($validator->fails()) {
            throw new JsonValidationException($validator);
        }
    }

    /**
     * The data that will be validated
     *
     * @return array
     */
    public function data()
    {
        return Input::all();
    }

    /**
     * Validation rules
     *
     * @return array
     */
    abstract public function rules();

    abstract public function messages();
}