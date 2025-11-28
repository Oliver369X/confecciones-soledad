<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    tipo_servicio: '',
    descripcion_prenda: '',
    telefono_contacto: '',
});

const enviarPedido = () => {
    form.post(route('cliente.guardar-pedido'), {
        onSuccess: () => {
            // El redirect lo maneja el controlador, o podemos mostrar un mensaje aquí
        }
    });
};
</script>

<template>
    <Head title="Hacer Pedido" />

    <AuthenticatedLayout page-title="Hacer Pedido">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 text-white mb-8 shadow-lg">
            <h1 class="text-3xl font-bold mb-2">Solicita Tu Servicio</h1>
            <p class="opacity-90">Cuéntanos qué necesitas y te contactaremos pronto</p>
        </div>

        <!-- Formulario -->
        <div class="bg-theme-card rounded-2xl shadow-lg p-8 border border-theme-border">
            <form @submit.prevent="enviarPedido">
                <div class="space-y-6">
                    <!-- Tipo de Servicio -->
                    <div>
                        <label class="block text-sm font-semibold text-theme-text mb-3">Tipo de Servicio</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button type="button"
                                    @click="form.tipo_servicio = 'CONFECCION'"
                                    :class="[
                                        'p-6 rounded-xl border-2 transition-all flex flex-col items-center justify-center',
                                        form.tipo_servicio === 'CONFECCION'
                                            ? 'border-purple-600 bg-purple-50 dark:bg-purple-900/20'
                                            : 'border-gray-200 dark:border-gray-700 hover:border-purple-400'
                                    ]">
                                <svg class="w-12 h-12 mb-2" :class="form.tipo_servicio === 'CONFECCION' ? 'text-purple-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <p class="font-semibold text-theme-text">Confección</p>
                                <p class="text-sm text-gray-500">Crear desde cero</p>
                            </button>

                            <button type="button"
                                    @click="form.tipo_servicio = 'ARREGLO'"
                                    :class="[
                                        'p-6 rounded-xl border-2 transition-all flex flex-col items-center justify-center',
                                        form.tipo_servicio === 'ARREGLO'
                                            ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-gray-200 dark:border-gray-700 hover:border-blue-400'
                                    ]">
                                <svg class="w-12 h-12 mb-2" :class="form.tipo_servicio === 'ARREGLO' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                </svg>
                                <p class="font-semibold text-theme-text">Arreglo</p>
                                <p class="text-sm text-gray-500">Modificar existente</p>
                            </button>
                        </div>
                        <p v-if="form.errors.tipo_servicio" class="text-red-600 text-sm mt-2">{{ form.errors.tipo_servicio }}</p>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-semibold text-theme-text mb-2">
                            Describe tu proyecto
                        </label>
                        <textarea id="descripcion"
                                  v-model="form.descripcion_prenda"
                                  rows="5"
                                  class="w-full border-2 border-gray-200 dark:border-gray-700 bg-theme-bg text-theme-text rounded-xl p-4 focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition"
                                  placeholder="Ej: Necesito ajustar un vestido de novia, achicarlo 2 tallas..."></textarea>
                        <p v-if="form.errors.descripcion_prenda" class="text-red-600 text-sm mt-2">{{ form.errors.descripcion_prenda }}</p>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-semibold text-theme-text mb-2">
                            Teléfono de Contacto
                        </label>
                        <input type="tel"
                               id="telefono"
                               v-model="form.telefono_contacto"
                               class="w-full border-2 border-gray-200 dark:border-gray-700 bg-theme-bg text-theme-text rounded-xl p-4 focus:border-purple-600 focus:ring-2 focus:ring-purple-200 transition"
                               placeholder="Ej: 75123456">
                        <p v-if="form.errors.telefono_contacto" class="text-red-600 text-sm mt-2">{{ form.errors.telefono_contacto }}</p>
                    </div>

                    <!-- Botón Submit -->
                    <div class="pt-4">
                        <button type="submit"
                                :disabled="form.processing"
                                class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 rounded-xl text-lg font-bold hover:shadow-2xl transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="!form.processing">Enviar Solicitud</span>
                            <span v-else>Enviando...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
