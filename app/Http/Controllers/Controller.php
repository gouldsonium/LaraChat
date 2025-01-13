<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function handleErrorResponse($response, string $defaultMessage)
    {
        $errorDetails = $response->json()['error']['message'] ?? $defaultMessage;
        session()->flash('flash.banner', $defaultMessage);
        session()->flash('flash.bannerStyle', 'danger');
        return back()->withErrors(['details' => $errorDetails])->withInput();
    }
}
