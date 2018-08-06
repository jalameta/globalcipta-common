<?php

if (! function_exists('api')) {
    /**
     * Api response helper
     *
     * @param \Illuminate\Contracts\Support\Arrayable $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function api(\Illuminate\Contracts\Support\Arrayable $data)
    {
        return response()->api($data);
    }
}

if (! function_exists('errorApi')) {
    /**
     * Error Api helper
     *
     * @param \GlobalCipta\Common\Response\ErrorApiResponse $error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function errorApi(\GlobalCipta\Common\Response\ErrorApiResponse $error)
    {
        return response()->errorApi($error);
    }
}
