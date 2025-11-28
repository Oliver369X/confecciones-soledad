<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import RevenueChart from '@/Components/Charts/RevenueChart.vue';

defineProps({
    stats: Object,
    chartData: Object,
    role: String
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout page-title="Dashboard">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl shadow-xl p-8 text-white mb-6">
            <h1 class="text-3xl font-bold mb-2">¡Bienvenido de vuelta! 👋</h1>
            <p class="opacity-90">Sistema de gestión - Confecciones Soledad</p>
        </div>

        <!-- VISTA CLIENTE -->
        <div v-if="role === 'CLIENTE'">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Pedidos Activos -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-theme-text opacity-70 text-sm font-medium">Pedidos Activos</p>
                            <p class="text-3xl font-bold text-theme-text mt-1">{{ stats.pedidos_activos }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <Link :href="route('cliente.mis-pedidos')" class="text-xs text-blue-500 hover:underline mt-4 block">Ver mis pedidos &rarr;</Link>
                </div>
                
                <!-- Total Gastado -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-theme-text opacity-70 text-sm font-medium">Total Invertido</p>
                            <p class="text-3xl font-bold text-theme-text mt-1">Bs {{ Number(stats.total_gastado).toFixed(2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas Cliente -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border border-theme-border flex flex-col justify-center space-y-3">
                    <h3 class="font-bold text-theme-text mb-2">¿Qué deseas hacer?</h3>
                    <Link :href="route('public.hacer-pedido')" class="w-full bg-primary text-white text-center py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-md">
                        + Nuevo Pedido
                    </Link>
                    <Link :href="route('public.catalogo')" class="w-full bg-white border-2 border-primary text-primary text-center py-3 rounded-lg hover:bg-gray-50 transition font-semibold">
                        Ver Catálogo
                    </Link>
                </div>
            </div>
        </div>

        <!-- VISTA ADMIN -->
        <div v-else>
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Card 1: Pedidos Hoy -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-theme-text opacity-70 text-sm font-medium">Pedidos Hoy</p>
                            <p class="text-3xl font-bold text-theme-text mt-1">{{ stats.pedidos_hoy }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-theme-text opacity-60 mt-4">Navega a Pedidos para gestionar</p>
                </div>

                <!-- Card 2: En Proceso -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-theme-text opacity-70 text-sm font-medium">En Proceso</p>
                            <p class="text-3xl font-bold text-theme-text mt-1">{{ stats.en_proceso }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-theme-text opacity-60 mt-4">Trabajos en progreso</p>
                </div>

                <!-- Card 3: Ingresos del Mes -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-theme-text opacity-70 text-sm font-medium">Ingresos del Mes</p>
                            <p class="text-3xl font-bold text-theme-text mt-1">Bs {{ Number(stats.ingresos_mes).toFixed(2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-theme-text opacity-60 mt-4">Ve a Reportes para detalles</p>
                </div>

                <!-- Card 4: Materiales Bajos -->
                <div class="bg-theme-card rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-theme-text opacity-70 text-sm font-medium">Stock Bajo</p>
                            <p class="text-3xl font-bold text-theme-text mt-1">{{ stats.stock_bajo }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-theme-text opacity-60 mt-4">Revisa Inventario</p>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-theme-card rounded-xl shadow-lg p-6 mb-6 border border-theme-border">
                <h3 class="text-lg font-bold text-theme-text mb-4">Ingresos Mensuales</h3>
                <RevenueChart v-if="chartData" :chartData="chartData" />
            </div>

            <!-- Quick Actions -->
            <div class="bg-theme-card rounded-xl shadow-lg p-6 border border-theme-border">
                <h2 class="text-xl font-bold text-theme-text mb-4">Acciones Rápidas</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a :href="route('orders.create')" class="flex items-center space-x-3 p-4 bg-theme-bg rounded-lg hover:opacity-80 transition border border-theme-border">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-theme-text">Nuevo Pedido</p>
                            <p class="text-xs text-theme-text opacity-70">Registrar trabajo</p>
                        </div>
                    </a>

                    <a :href="route('inventory.index')" class="flex items-center space-x-3 p-4 bg-theme-bg rounded-lg hover:opacity-80 transition border border-theme-border">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-theme-text">Ver Inventario</p>
                            <p class="text-xs text-theme-text opacity-70">Materiales y stock</p>
                        </div>
                    </a>

                    <a :href="route('reports.index')" class="flex items-center space-x-3 p-4 bg-theme-bg rounded-lg hover:opacity-80 transition border border-theme-border">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-theme-text">Reportes</p>
                            <p class="text-xs text-theme-text opacity-70">Estadísticas y métricas</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-theme-bg border-l-4 border-blue-500 p-6 rounded-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-theme-text font-semibold">Sistema Completo</h3>
                    <p class="text-theme-text opacity-80 text-sm mt-1">
                        Usa el menú lateral para navegar entre módulos. Todas las funcionalidades están disponibles.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
