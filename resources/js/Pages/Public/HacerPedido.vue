<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50">
        <!-- Navbar -->
        <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link :href="route('public.home')" class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-2 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                            Confecciones Soledad
                        </span>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 py-20">
            <div class="max-w-4xl mx-auto text-center px-4">
                <h1 class="text-5xl font-bold text-white mb-6">Solicita Tu Servicio</h1>
                <p class="text-xl text-purple-100">Cuéntanos qué necesitas y te contactaremos pronto</p>
            </div>
        </div>

        <!-- Formulario -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="bg-white rounded-3xl shadow-2xl p-10">
                <form @submit.prevent="enviarPedido">
                    <div class="space-y-6">
                        <!-- Tipo de Servicio -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-3">Tipo de Servicio</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button type="button"
                                        @click="form.tipo_servicio = 'CONFECCION'"
                                        :class="[
                                            'p-6 rounded-xl border-2 transition-all',
                                            form.tipo_servicio === 'CONFECCION'
                                                ? 'border-purple-600 bg-purple-50'
                                                : 'border-gray-300 hover:border-purple-400'
                                        ]">
                                    <svg class="w-12 h-12 mx-auto mb-2" :class="form.tipo_servicio === 'CONFECCION' ? 'text-purple-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                    <p class="font-semibold text-gray-900">Confección</p>
                                    <p class="text-sm text-gray-500">Crear desde cero</p>
                                </button>

                                <button type="button"
                                        @click="form.tipo_servicio = 'ARREGLO'"
                                        :class="[
                                            'p-6 rounded-xl border-2 transition-all',
                                            form.tipo_servicio === 'ARREGLO'
                                                ? 'border-blue-600 bg-blue-50'
                                                : 'border-gray-300 hover:border-blue-400'
                                        ]">
                                    <svg class="w-12 h-12 mx-auto mb-2" :class="form.tipo_servicio === 'ARREGLO' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                    </svg>
                                    <p class="font-semibold text-gray-900">Arreglo</p>
                                    <p class="text-sm text-gray-500">Modificar existente</p>
                                </button>
                            </div>
                            <p v-if="form.errors.tipo_servicio" class="text-red-600 text-sm mt-2">{{ form.errors.tipo_servicio }}</p>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-semibold text-gray-900 mb-2">
                                Describe tu proyecto
                            </label>
                            <textarea id="descripcion"
                                      v-model="form.descripcion_prenda"
                                      rows="5"
                                      class="w-full border-2 border-gray-300 rounded-xl p-4 focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition"
                                      placeholder="Ej: Necesito ajustar un vestido de novia, achicarlo 2 tallas..."></textarea>
                            <p v-if="form.errors.descripcion_prenda" class="text-red-600 text-sm mt-2">{{ form.errors.descripcion_prenda }}</p>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-semibold text-gray-900 mb-2">
                                Teléfono de Contacto
                            </label>
                            <input type="tel"
                                   id="telefono"
                                   v-model="form.telefono_contacto"
                                   class="w-full border-2 border-gray-300 rounded-xl p-4 focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition"
                                   placeholder="Ej: 75123456">
                            <p v-if="form.errors.telefono_contacto" class="text-red-600 text-sm mt-2">{{ form.errors.telefono_contacto }}</p>
                        </div>

                        <!-- Botón Submit -->
                        <div class="pt-4">
                            <button type="submit"
                                    :disabled="form.processing"
                                    class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 rounded-xl text-lg font-bold hover:shadow-2xl transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="!form.processing">Enviar Solicitud</span>
                                <span v-else>Enviando...</span>
                            </button>
                        </div>

                        <!-- Info adicional -->
                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                            <h4 class="font-semibold text-purple-900 mb-2">📞 ¿Qué sigue?</h4>
                            <ul class="text-sm text-purple-800 space-y-1">
                                <li>✓ Revisaremos tu solicitud</li>
                                <li>✓ Te contactaremos en menos de 24 horas</li>
                                <li>✓ Te daremos un presupuesto personalizado</li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    tipo_servicio: '',
    descripcion_prenda: '',
    telefono_contacto: '',
});

const enviarPedido = () => {
    form.post(route('public.guardar-pedido'), {
        onSuccess: () => {
            // Redirigirá a la página de gracias
        }
    });
};
</script>
