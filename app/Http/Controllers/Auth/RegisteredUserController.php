<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        \Log::info('🔵 [REGISTRO] Iniciando proceso de registro', [
            'datos_recibidos' => $request->except('password', 'password_confirmation')
        ]);

        try {
            $validated = $request->validate([
                'nombre_completo' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'telefono' => 'nullable|string|max:20',
            ]);

            \Log::info('✅ [REGISTRO] Validación exitosa');

            $user = User::create([
                'nombre_completo' => $request->nombre_completo,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'CLIENTE', // Siempre CLIENTE en registro público
                'telefono' => $request->telefono,
            ]);

            \Log::info('✅ [REGISTRO] Usuario creado exitosamente', [
                'usuario_id' => $user->usuario_id,
                'email' => $user->email,
                'rol' => $user->rol,
            ]);

            event(new Registered($user));

            Auth::login($user);

            \Log::info('✅ [REGISTRO] Usuario autenticado. Redirigiendo a Mi Cuenta');

            return redirect(route('cliente.mi-cuenta', absolute: false));

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ [REGISTRO] Error de validación', [
                'errores' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('❌ [REGISTRO] Error grave en el registro', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
            throw $e;
        }
    }
}
