<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center px-4">
        <div class="max-w-2xl w-full">
            <div class="bg-white rounded-3xl shadow-2xl p-12 text-center">
                <!-- Icono Success -->
                <div class="mb-8">
                    <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-4">¡Solicitud Enviada!</h1>
                <p class="text-xl text-gray-600 mb-8">
                    Hemos recibido tu pedido correctamente. Nos pondremos en contacto contigo muy pronto.
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                    <p class="text-blue-900 font-semibold mb-2">📱 Próximos Pasos:</p>
                    <ul class="text-blue-800 text-left space-y-2">
                        <li>✓ Revisaremos los detalles de tu solicitud</li>
                        <li>✓ Te llamaremos en menos de 24 horas</li>
                        <li>✓ Coordinaremos una cita si es necesario</li>
                        <li>✓ Te enviaremos un presupuesto personalizado</li>
                    </ul>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route('public.home')" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-full font-semibold hover:shadow-lg transition">
                        Volver al Inicio
                    </Link>
                    <Link :href="route('public.catalogo')" class="border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-full font-semibold hover:bg-purple-50 transition">
                        Ver Catálogo
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
