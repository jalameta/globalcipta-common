<?php

namespace GlobalCipta\Common\Response;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Error response object
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class ErrorApiResponse implements ArrayAccess, JsonSerializable, Arrayable, Jsonable
{
    const ACCOUNT_ALREADY_EXISTS = 'ACCOUNT_ALREADY_EXISTS';
    const ACCOUNT_IS_DISABLED = 'ACCOUNT_IS_DISABLED';
    const AUTHENTICATION_FAILED = 'AUTHENTICATION_FAILED';
    const INSUFFICIENT_ACCOUNT_PERMISSIONS = 'INSUFFICIENT_ACCOUNT_PERMISSIONS';
    const INTERNAL_ERROR = 'INTERNAL_ERROR';
    const INVALID_INPUT = 'INVALID_INPUT';
    const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    const RESOURCE_ALREADY_EXISTS = 'RESOURCE_ALREADY_EXISTS';
    const SERVER_BUSY = 'SERVER_BUSY';
    const OPERATION_TIME_OUT = 'OPERATION_TIME_OUT';

    /**
     * List of all status code messages
     *
     * @var array
     */
    protected $statusCode = [
        self::ACCOUNT_ALREADY_EXISTS => 'The specified account already exists',
        self::ACCOUNT_IS_DISABLED => 'The specified account is disabled',
        self::AUTHENTICATION_FAILED => 'Server failed to authenticate the request. Make sure the value of the Authorization header is formed correctly including the signature.',
        self::INSUFFICIENT_ACCOUNT_PERMISSIONS => 'The account being accessed does not have sufficient permissions to execute this operation.',
        self::INTERNAL_ERROR => 'The server encountered an internal error. Please retry the request.',
        self::INVALID_INPUT => 'One or more of the request inputs is not valid.',
        self::RESOURCE_NOT_FOUND => 'The specified resource does not exists.',
        self::RESOURCE_ALREADY_EXISTS => 'The specified resource already exists',
        self::SERVER_BUSY => 'The server is currently unable to receive requests. please retry your request',
        self::OPERATION_TIME_OUT => 'The operation could not be completed within the permitted time',
    ];

    /**
     * List of http code used by the status
     *
     * @var array
     */
    protected $httpStatusCode = [
        304 => [],
        400 => [
            self::INVALID_INPUT
        ],
        403 => [
            self::ACCOUNT_IS_DISABLED,
            self::AUTHENTICATION_FAILED,
            self::INSUFFICIENT_ACCOUNT_PERMISSIONS
        ],
        404 => [],
        409 => [
            self::ACCOUNT_ALREADY_EXISTS
        ],
        500 => [
            self::INTERNAL_ERROR
        ],
        503 => []
    ];

    /**
     * Base response format
     *
     * @var array
     */
    protected $messages = [
        'status_code' => '',
        'http_status' => '',
        'message' => '',
        'data' => null
    ];

    /**
     * ErrorApiResponse constructor.
     *
     * @param      $statusCode
     * @param null $data
     */
    public function __construct($statusCode, $data = null)
    {
        if (array_key_exists($statusCode, $this->statusCode) === false)
            throw new \RuntimeException("Invalid ErrorApiResponse status code");

        $this->messages['status_code'] = $statusCode;
        $this->messages['data'] = $data;
        $this->messages['message'] = $this->statusCode[$statusCode];

        foreach ($this->httpStatusCode as $httpStatus => $statuses)
        {
            if (in_array($statusCode, $statuses))
                $this->messages['http_status'] = $httpStatus;
        }
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->messages['message'];
    }

    /**
     * Get Http Status
     *
     * @return integer
     */
    public function getHttpStatus()
    {
        return $this->messages['http_status'];
    }

    /**
     * Get error status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->messages['status_code'];
    }

    /**
     * Get error data
     *
     * @return mixed
     */
    public function getErrorData()
    {
        return $this->messages['data'];
    }

    /**
     * Create new Error Api Response
     *
     * @param      $statusCode
     * @param null $data
     *
     * @return \GlobalCipta\Common\Response\ErrorApiResponse
     */
    public static function make($statusCode, $data = null)
    {
        return new static([
            'status_code' => $statusCode,
            'data' => $data
        ]);
    }

    /**
     * Convert error instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->messages;
    }


    /**
     * Whether an offset exists
     *
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->messages[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->messages[$offset]);
    }

    /**
     * Get an attribute from the container.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->messages)) {
            return $this->messages[$key];
        }

        return value($default);
    }
}
