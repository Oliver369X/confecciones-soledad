<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--nombre= : Nombre completo del administrador}
                            {--email= : Email del administrador}
                            {--password= : Contraseña (opcional, se generará una aleatoria)}
                            {--rol=PROPIETARIO : Rol del usuario (PROPIETARIO o AYUDANTE)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuario administrador (PROPIETARIO o AYUDANTE)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Creando usuario administrador...');
        $this->newLine();

        // Obtener datos
        $nombre = $this->option('nombre') ?: $this->ask('Nombre completo');
        $email = $this->option('email') ?: $this->ask('Email');
        $rol = $this->option('rol');
        
        // Validar rol
        if (!in_array($rol, ['PROPIETARIO', 'AYUDANTE'])) {
            $this->error('❌ Rol debe ser PROPIETARIO o AYUDANTE');
            return 1;
        }

        // Verificar si email ya existe
        if (User::where('email', $email)->exists()) {
            $this->error('❌ El email ya está registrado');
            return 1;
        }

        // Contraseña
        $password = $this->option('password');
        if (!$password) {
            $password = $this->secret('Contraseña (deja vacío para generar una aleatoria)');
            if (!$password) {
                $password = \Str::random(12);
                $generada = true;
            } else {
                $generada = false;
            }
        } else {
            $generada = false;
        }

        // Teléfono opcional
        $telefono = $this->ask('Teléfono (opcional)');

        // Crear usuario
        try {
            $user = User::create([
                'nombre_completo' => $nombre,
                'email' => $email,
                'password' => Hash::make($password),
                'rol' => $rol,
                'telefono' => $telefono,
            ]);

            $this->newLine();
            $this->info('✅ Usuario creado exitosamente!');
            $this->newLine();
            
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $user->usuario_id],
                    ['Nombre', $user->nombre_completo],
                    ['Email', $user->email],
                    ['Rol', $user->rol],
                    ['Teléfono', $user->telefono ?: 'N/A'],
                ]
            );

            if ($generada) {
                $this->newLine();
                $this->warn('⚠️  Contraseña generada (guárdala):');
                $this->line('   ' . $password);
            }

            $this->newLine();
            $this->info('🔐 Puedes iniciar sesión en:');
            $this->line('   http://localhost:8000/login');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al crear usuario: ' . $e->getMessage());
            return 1;
        }
    }
}
