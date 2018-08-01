<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionTrait
{
    /**
     * Custormized Exceptions.
     * 
     */
    public function apiException($request, $exception)
    {
        if($this->isModel($exception))
        {
            return $this->ModelResponse($exception);
        }

        if($this->isHttp($exception))
        {
            return $this->HttpResponse($exception);
        }

        return parent::render($request, $exception);
    }

    protected function isModel($exception)
    {
        return $exception instanceof ModelNotFoundException;
    }

    protected function isHttp($exception)
    {
        return $exception instanceof NotFoundHttpException;
    }

    protected function ModelResponse()
    {
        return response()->json([
            'errors' => 'this is not the product you are looking for'
        ], 404);
    }

    protected function HttpResponse()
    {
        return response()->json([
            'errors' => 'this is not the page you are looking for'
        ], 404);
    }
}