<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight">Mi Cuenta</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 mb-8 text-white">
                    <h3 class="text-3xl font-bold mb-2">¡Bienvenido, {{ $page.props.auth.user.nombre_completo }}!</h3>
                    <p class="text-purple-100">Aquí puedes gestionar tus pedidos y ver tu historial</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Pedidos -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">Total Pedidos</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ totalPedidos }}</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pedidos Activos -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">En Proceso</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ pedidosActivos }}</p>
                            </div>
                            <div class="bg-blue-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completados -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">Completados</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ totalPedidos - pedidosActivos }}</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid md:grid-cols-2 gap-6">
                    <Link :href="route('cliente.mis-pedidos')" class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="bg-purple-100 p-4 rounded-full">
                                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Ver Mis Pedidos</h4>
                                <p class="text-gray-600">Revisa el estado de todos tus pedidos</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('public.hacer-pedido')" class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl shadow-lg p-8 text-white hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 p-4 rounded-full">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold">Nuevo Pedido</h4>
                                <p class="text-purple-100">Solicita un nuevo servicio</p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    totalPedidos: Number,
    pedidosActivos: Number,
});
</script>
