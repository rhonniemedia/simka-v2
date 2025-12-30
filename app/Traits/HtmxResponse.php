<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait HtmxResponse
{
    protected function successResponse(string $event, string $message, string $title = 'Berhasil!')
    {
        return response('', 204)->header('HX-Trigger', json_encode([
            $event => null,
            'showAlert' => [
                'icon' => 'success',
                'title' => $title,
                'text' => $message
            ]
        ]));
    }

    protected function infoResponse(string $title, string $message, string $event = 'productUpdated')
    {
        return response('', 204)->header('HX-Trigger', json_encode([
            $event => null,  // PENTING: Event trigger untuk tutup modal
            'showAlert' => [
                'icon' => 'info',
                'title' => $title,
                'text' => $message
            ]
        ]));
    }

    protected function validationErrorResponse($model, \Illuminate\Validation\ValidationException $e)
    {
        // Support both variable names for backward compatibility
        $view = view('partials.form', [
            'product' => $model,
            'model' => $model
        ]);
        return $view->withErrors($e->validator);
    }
}
