<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Błędy walidacji
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'error' => 'Błąd walidacji danych',
                'details' => $e->errors()
            ], 422);
        });

        // Błędy bazy danych
        $exceptions->render(function (QueryException $e, Request $request) {
            Log::error('Błąd bazy danych', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return response()->json([
                'error' => 'Wystąpił błąd bazy danych. Spróbuj ponownie później.'
            ], 500);
        });

        // Błędy sieciowe
        $exceptions->render(function (HttpException $e, Request $request) {
            return response()->json([
                'error' => 'Problem z komunikacją sieciową',
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        });

        // Błędy autoryzacji
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'error' => 'Brak uwierzytelnienia. Zaloguj się, aby kontynuować.'
            ], 401);
        });

        // Błędy autoryzacji (brak uprawnień)
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->json([
                'error' => 'Brak uprawnień do wykonania tej operacji.'
            ], 403);
        });

        // Błędy związane z brakiem danych
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return response()->json([
                'error' => 'Nie znaleziono żądanego zasobu.'
            ], 404);
        });
    })->create();
