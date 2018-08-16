<?php

namespace GlobalCipta\Common\Bus;

use Closure;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Validator;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Factory as ValidationFactory;

/**
 * Base Job/Command Bus.
 *
 * @author      veelasky <veelasky@gmail.com>
 *
 * @property \Illuminate\Http\Request $request
 * @property \Illuminate\Validation\Factory $validation
 */
abstract class BaseJob
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    const STATUS_IDLE = 'idle';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCESS = 'success';
    const STATUS_RUNNING = 'running';

    protected $status = self::STATUS_IDLE;

    /**
     * List of all registered callbacks.
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * Original input data.
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Custom validation error messages.
     *
     * @var array
     */
    protected $messages = [];


    /**
     * The default error bag.
     *
     * @var string
     */
    protected $validatesRequestErrorBag;

    /**
     * Job constructor.
     *
     * @param array $inputs
     */
    public function __construct(array $inputs = [])
    {
        $request = app('request');
        $request->merge($inputs);
        $this->inputs = $request->all();
    }

    /**
     * Make dynamic call to the command.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'on')) {
            $event = substr($name, 2);

            return $this->registerCallback(Str::lower($event), $arguments[0]);
        }

        throw new BadMethodCallException("Invalid Method `$name`");
    }

    /**
     * Handle incoming job.
     *
     * @return $this
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle()
    {
        event('job.running', $this);

        // Determine if this command has a boot method, which is convenient when developer
        // needs to modify any information on this command before actually running it.
        if (method_exists($this, 'boot')) {
            $this->boot();
        }

        $this->fireCallbacks('running');
        $this->validate();

        $outcome = $this->run();

        $this->status = ($outcome) ? self::STATUS_SUCCESS : self::STATUS_FAILED;

        // base on the outcome of the run method, let's run additional callbacks.
        $this->fireCallbacks($this->status);

        event('job.finished', $this);

        return $this;
    }

    /**
     * Run the actual command process.
     *
     * @return mixed
     */
    abstract public function run();

    /**
     * Immediately calling abort callbacks.
     *
     * @return void
     */
    public function abort()
    {
        $this->fireCallbacks('abort');
    }

    /**
     * Register Callback to the callbacks array.
     *
     * @param          $event
     * @param \Closure $callback
     *
     * @return $this
     */
    public function registerCallback($event, Closure $callback)
    {
        $this->callbacks[$event][] = $callback;

        return $this;
    }

    /**
     * Determine if job is succeeded.
     *
     * @return bool
     */
    public function success()
    {
        return ($this->status === self::STATUS_SUCCESS);
    }

    /*
     * Determine if job is Failed
     *
     * @return bool
     */
    public function failed()
    {
        return ($this->status === self::STATUS_FAILED);
    }

    /**
     * Validate incoming request against given rule.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        $validator = $this->getValidationFactory()->make($this->request->all(), $this->getValidationRules(), $this->getCustomValidationErrorMessages());

        if ($validator->fails()) {
            $this->abort();

            $this->throwValidationException($this->request, $validator);
        }
    }

    /**
     * Get validation factory.
     *
     * @return \Illuminate\Validation\Factory
     */
    public function getValidationFactory()
    {
        return $this->validation instanceof ValidationFactory
                    ? $this->validation
                    : app('validator');
    }

    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return (property_exists($this, 'rules')) ? $this->rules : [];
    }

    /**
     * Get custom ValidationError Messages
     * @return array
     */
    public function getCustomValidationErrorMessages()
    {
        return (property_exists($this, 'messages')) ? $this->messages : [];
    }


    /**
     * Fire all callbacks registered on the callbacks array.
     *
     * @param string $event
     *
     * @return void
     */
    protected function fireCallbacks($event)
    {
        if (array_key_exists($event, $this->callbacks)) {
            foreach ($this->callbacks[$event] as $callback) {
                call_user_func($callback, $this);
            }
        }
    }

    /**
     * Throw the failed validation exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwValidationException(Request $request, Validator $validator)
    {
        throw new ValidationException($validator, $this->buildFailedValidationResponse(
            $request, $validator->errors()->getMessages()
        ));
    }

    /**
     * Create the response for when a request fails validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if ($request->expectsJson()) {
            return new JsonResponse([
                'status' => 'ERR_VALIDATION_FAILED',
                'data' => $errors,
            ], 422);
        }

        return redirect()->to($this->getRedirectUrl())
                         ->withInput($request->input())
                         ->withErrors($errors, $this->errorBag());
    }

    /**
     * Get the URL we should redirect to.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return app(UrlGenerator::class)->previous();
    }

    /**
     * Get the key to be used for the view error bag.
     *
     * @return string
     */
    protected function errorBag()
    {
        return $this->validatesRequestErrorBag ?: 'default';
    }

    /**
     * Get dynamic property handler
     *
     * @return mixed
     */
    public function __get($name)
    {
        switch($name)
        {
            case 'request':
                $request = app('request');
                $request->merge($this->inputs);

                return $request;
                break;
            case 'validation';
                return app('validator');
                break;
            default:
                return null;
                break;
        }
    }
}
