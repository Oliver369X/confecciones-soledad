<?php

namespace App\Http\Middleware;

use App\Models\VisitaPagina;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContadorVisitas
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo contar visitas GET (no POST, PUT, DELETE)
        if ($request->isMethod('GET')) {
            $pagina = $request->path();
            VisitaPagina::incrementar($pagina);
        }

        return $next($request);
    }
}
