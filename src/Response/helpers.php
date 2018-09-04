<?php

if (! function_exists('api')) {
    /**
     * Api response helper.
     *
     * @param     $data
     * @param int $httpCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function api($data, $httpCode = 200)
    {
        return response()->api($data, $httpCode);
    }
}

if (! function_exists('errorApi')) {
    /**
     * Error Api helper.
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
