<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

trait HtmxResponse
{
    /**
     * Respon sukses dengan trigger HTMX dan SweetAlert.
     */
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

    /**
     * Respon info dengan trigger HTMX.
     */
    protected function infoResponse(string $title, string $message, string $event)
    {
        return response('', 204)->header('HX-Trigger', json_encode([
            $event => null,
            'showAlert' => [
                'icon' => 'info',
                'title' => $title,
                'text' => $message
            ]
        ]));
    }

    /**
     * Respon validasi error global.
     * * @param Model $model Objek model (Product, User, dll)
     * @param ValidationException $e Exception dari validator
     * @param string $viewPath Path file blade (contoh: 'partials.form')
     * @param string $modelAlias Nama variabel spesifik di view (contoh: 'product')
     * @return \Illuminate\View\View
     */
    protected function validationErrorResponse(Model $model, ValidationException $e, string $viewPath, string $modelAlias = 'model')
    {
        /** @var \Illuminate\View\View $view */
        $view = view($viewPath, [
            $modelAlias => $model, // Mendukung variabel spesifik seperti $product
            'model'     => $model, // Mendukung variabel generic $model
        ]);

        return $view->withErrors($e->validator);
    }
}
