<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    order: Object,
});

const getStatusColor = (status) => {
    switch (status) {
        case 'PENDIENTE_PRESUPUESTO': return 'bg-yellow-100 text-yellow-800';
        case 'APROBADO': return 'bg-blue-100 text-blue-800';
        case 'EN_PROCESO': return 'bg-indigo-100 text-indigo-800';
        case 'LISTO_ENTREGA': return 'bg-green-100 text-green-800';
        case 'ENTREGADO': return 'bg-gray-100 text-gray-800';
        case 'CANCELADO': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Detalle del Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detalle del Pedido #{{ order.pedido_id }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información General -->
                            <div>
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Información General</h3>
                                <div class="space-y-3">
                                    <p><span class="font-bold">Cliente:</span> {{ order.cliente?.nombre_completo }}</p>
                                    <p><span class="font-bold">Email:</span> {{ order.cliente?.email }}</p>
                                    <p><span class="font-bold">Servicio:</span> {{ order.tipo_servicio }}</p>
                                    <p><span class="font-bold">Estado:</span> 
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full ml-2', getStatusColor(order.estado)]">
                                            {{ order.estado }}
                                        </span>
                                    </p>
                                    <p><span class="font-bold">Fecha Solicitud:</span> {{ new Date(order.fecha_solicitud).toLocaleDateString() }}</p>
                                    <p v-if="order.fecha_entrega_estimada"><span class="font-bold">Entrega Estimada:</span> {{ new Date(order.fecha_entrega_estimada).toLocaleDateString() }}</p>
                                    <p v-if="order.fecha_entregado"><span class="font-bold">Entregado el:</span> {{ new Date(order.fecha_entregado).toLocaleDateString() }}</p>
                                </div>
                            </div>

                            <!-- Información Financiera -->
                            <div>
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Información Financiera</h3>
                                <div class="space-y-3">
                                    <p><span class="font-bold">Presupuesto Total:</span> Bs {{ order.presupuesto_total }}</p>
                                    <p v-if="order.monto_descuento > 0" class="text-green-600"><span class="font-bold">Descuento:</span> - Bs {{ order.monto_descuento }}</p>
                                    <p class="text-xl font-bold mt-4">Total a Pagar: Bs {{ order.presupuesto_total - order.monto_descuento }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Descripción de la Prenda</h3>
                            <p class="bg-gray-50 dark:bg-gray-700 p-4 rounded">{{ order.descripcion_prenda }}</p>
                        </div>

                        <!-- Botones de Acción (Ejemplo) -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <Link :href="route('orders.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Volver
                            </Link>
                            <!-- Aquí irían botones para cambiar estado, registrar pago, etc. -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
