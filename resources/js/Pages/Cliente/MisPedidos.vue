<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight">Mis Pedidos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Lista de Pedidos -->
                <div v-if="pedidos.data && pedidos.data.length > 0" class="space-y-4">
                    <div v-for="pedido in pedidos.data" :key="pedido.pedido_id"
                         class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="text-gray-500 font-mono">#{pedido.pedido_id}</span>
                                    <span :class="getEstadoClass(pedido.estado)" class="px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ getEstadoLabel(pedido.estado) }}
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ pedido.tipo_servicio === 'ARREGLO' ? '🔧 Arreglo' : '✂️ Confección' }}
                                </h3>
                                <p class="text-gray-600 mb-4">{{ pedido.descripcion_prenda }}</p>
                                
                                <div class="grid md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Fecha Solicitud</p>
                                        <p class="font-semibold">{{ formatDate(pedido.fecha_solicitud) }}</p>
                                    </div>
                                    <div v-if="pedido.presupuesto_total > 0">
                                        <p class="text-gray-500">Presupuesto</p>
                                        <p class="font-semibold text-green-600">Bs. {{ pedido.presupuesto_total }}</p>
                                    </div>
                                    <div v-if="pedido.fecha_entrega_estimada">
                                        <p class="text-gray-500">Entrega Estimada</p>
                                        <p class="font-semibold">{{ formatDate(pedido.fecha_entrega_estimada) }}</p>
                                    </div>
                                </div>
                            </div>

                            <Link :href="route('cliente.ver-pedido', pedido.pedido_id)" 
                                  class="ml-4 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                                Ver Detalle →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Sin Pedidos -->
                <div v-else class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No tienes pedidos aún</h3>
                    <p class="text-gray-600 mb-6">¡Empieza haciendo tu primer solicitud!</p>
                    <Link :href="route('public.hacer-pedido')" class="inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transition">
                        Hacer Pedido
                    </Link>
                </div>

                <!-- Paginación -->
                <div v-if="pedidos.links && pedidos.links.length > 3" class="flex justify-center space-x-2 mt-8">
                    <Link v-for="link in pedidos.links" :key="link.label"
                          :href="link.url"
                          :class="[
                              'px-4 py-2 rounded-lg font-semibold transition',
                              link.active 
                                  ? 'bg-purple-600 text-white' 
                                  : 'bg-white text-gray-700 hover:bg-gray-100'
                          ]"
                          v-html="link.label">
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
    pedidos: Object,
});

const getEstadoClass = (estado) => {
    const classes = {
        'PENDIENTE_PRESUPUESTO': 'bg-yellow-100 text-yellow-800',
        'CONFIRMADO': 'bg-blue-100 text-blue-800',
        'EN_PROCESO': 'bg-purple-100 text-purple-800',
        'LISTO_ENTREGAR': 'bg-green-100 text-green-800',
        'ENTREGADO': 'bg-gray-100 text-gray-800',
        'CANCELADO': 'bg-red-100 text-red-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'PENDIENTE_PRESUPUESTO': 'Pendiente Presupuesto',
        'CONFIRMADO': 'Confirmado',
        'EN_PROCESO': 'En Proceso',
        'LISTO_ENTREGAR': 'Listo para Entregar',
        'ENTREGADO': 'Entregado',
        'CANCELADO': 'Cancelado',
    };
    return labels[estado] || estado;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>
