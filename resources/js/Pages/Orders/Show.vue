<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    order: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const canEdit = computed(() => ['PROPIETARIO', 'AYUDANTE'].includes(user.value?.rol));
const isClient = computed(() => user.value?.rol === 'CLIENTE');

// Formulario para actualizar estado y presupuesto (Admin)
const formUpdate = useForm({
    estado: props.order.estado,
    presupuesto_total: props.order.presupuesto_total,
    numero_cuotas: props.order.numero_cuotas || 1,
});

const updateOrder = () => {
    formUpdate.put(route('orders.update', props.order.pedido_id), {
        preserveScroll: true,
        onSuccess: () => {
            // Notificación
        },
    });
};

// Formulario para descuento (Cliente)
const formDiscount = useForm({
    codigo: ''
});

const applyDiscount = () => {
    formDiscount.post(route('orders.apply-discount', props.order.pedido_id), {
        preserveScroll: true,
        onSuccess: () => formDiscount.reset()
    });
};

// Generar QR
const generateQr = () => {
    router.post(route('payments.generate-qr'), {
        pedido_id: props.order.pedido_id
    });
};

const statusOptions = [
    { value: 'PENDIENTE_PRESUPUESTO', label: 'Pendiente Presupuesto' },
    { value: 'CONFIRMADO', label: 'Confirmado' },
    { value: 'EN_PROCESO', label: 'En Proceso' },
    { value: 'LISTO_ENTREGAR', label: 'Listo para Entregar' },
    { value: 'ENTREGADO', label: 'Entregado' },
    { value: 'CANCELADO', label: 'Cancelado' },
];

const getStatusColor = (status) => {
    switch (status) {
        case 'PENDIENTE_PRESUPUESTO': return 'bg-yellow-100 text-yellow-800';
        case 'CONFIRMADO': return 'bg-blue-100 text-blue-800';
        case 'EN_PROCESO': return 'bg-indigo-100 text-indigo-800';
        case 'LISTO_ENTREGAR': return 'bg-green-100 text-green-800';
        case 'ENTREGADO': return 'bg-gray-100 text-gray-800';
        case 'CANCELADO': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

// Cálculos
const totalPagado = computed(() => {
    return props.order.pagos?.filter(p => p.qr_status === 'PAID').reduce((acc, curr) => acc + parseFloat(curr.monto), 0) || 0;
});

const saldoPendiente = computed(() => {
    const total = parseFloat(props.order.presupuesto_total) - parseFloat(props.order.monto_descuento);
    return Math.max(0, total - totalPagado.value);
});

const progresoPago = computed(() => {
    const total = parseFloat(props.order.presupuesto_total) - parseFloat(props.order.monto_descuento);
    if (total <= 0) return 0;
    return Math.min(100, (totalPagado.value / total) * 100);
});
</script>

<template>
    <Head title="Detalle del Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-theme-text leading-tight">Pedido #{{ order.pedido_id }}</h2>
                <span :class="['px-3 py-1 rounded-full text-sm font-bold', getStatusColor(order.estado)]">
                    {{ order.estado.replace('_', ' ') }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Columna Izquierda: Detalles -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Info General -->
                        <div class="bg-theme-card overflow-hidden shadow-lg rounded-xl border border-theme-border p-6">
                            <h3 class="text-lg font-bold text-theme-text mb-4 border-b border-theme-border pb-2">Información del Pedido</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-theme-text opacity-70">Cliente</p>
                                    <p class="font-semibold text-theme-text">{{ order.cliente?.nombre_completo }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-theme-text opacity-70">Fecha Solicitud</p>
                                    <p class="font-semibold text-theme-text">{{ new Date(order.fecha_solicitud).toLocaleDateString() }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-theme-text opacity-70">Tipo Servicio</p>
                                    <p class="font-semibold text-theme-text">{{ order.tipo_servicio }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-theme-text opacity-70">Entrega Estimada</p>
                                    <p class="font-semibold text-theme-text">{{ order.fecha_entrega_estimada ? new Date(order.fecha_entrega_estimada).toLocaleDateString() : 'Por definir' }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-theme-text opacity-70">Descripción</p>
                                <p class="bg-theme-bg p-3 rounded-lg mt-1 text-theme-text">{{ order.descripcion_prenda }}</p>
                            </div>
                        </div>

                        <!-- Historial de Pagos -->
                        <div class="bg-theme-card overflow-hidden shadow-lg rounded-xl border border-theme-border p-6">
                            <h3 class="text-lg font-bold text-theme-text mb-4 border-b border-theme-border pb-2">Historial de Pagos</h3>
                            <div v-if="order.pagos && order.pagos.length > 0" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-theme-text opacity-70 uppercase">Fecha</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-theme-text opacity-70 uppercase">Monto</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-theme-text opacity-70 uppercase">Método</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-theme-text opacity-70 uppercase">Estado</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-theme-text opacity-70 uppercase">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="pago in order.pagos" :key="pago.pago_id">
                                            <td class="px-4 py-2 text-sm text-theme-text">{{ new Date(pago.created_at).toLocaleDateString() }}</td>
                                            <td class="px-4 py-2 text-sm font-bold text-theme-text">Bs {{ pago.monto }}</td>
                                            <td class="px-4 py-2 text-sm text-theme-text">{{ pago.metodo_pago }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <span v-if="pago.qr_status === 'PAID'" class="text-green-600 font-bold">PAGADO</span>
                                                <span v-else class="text-yellow-600">PENDIENTE</span>
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <a v-if="pago.qr_status === 'PAID'" 
                                                   :href="route('payments.invoice', pago.pago_id)" 
                                                   class="text-blue-600 hover:underline text-sm font-semibold">
                                                    Descargar Recibo
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-else class="text-theme-text opacity-60 text-center py-4">No hay pagos registrados.</p>
                        </div>
                    </div>

                    <!-- Columna Derecha: Acciones y Finanzas -->
                    <div class="space-y-6">
                        <!-- Resumen Financiero -->
                        <div class="bg-theme-card shadow-lg rounded-xl border border-theme-border p-6">
                            <h3 class="text-lg font-bold text-theme-text mb-4">Resumen Financiero</h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-theme-text">
                                    <span>Presupuesto Total</span>
                                    <span class="font-bold">Bs {{ Number(order.presupuesto_total).toFixed(2) }}</span>
                                </div>
                                <div v-if="order.monto_descuento > 0" class="flex justify-between text-green-600">
                                    <span>Descuento ({{ order.codigo_promocion }})</span>
                                    <span class="font-bold">- Bs {{ Number(order.monto_descuento).toFixed(2) }}</span>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                                <div class="flex justify-between text-lg font-bold text-theme-text">
                                    <span>Total a Pagar</span>
                                    <span>Bs {{ (order.presupuesto_total - order.monto_descuento).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-theme-text">
                                    <span>Pagado</span>
                                    <span class="text-green-600 font-bold">Bs {{ totalPagado.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-red-500 font-bold text-xl bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                                    <span>Saldo Pendiente</span>
                                    <span>Bs {{ saldoPendiente.toFixed(2) }}</span>
                                </div>
                            </div>

                            <!-- Barra de Progreso -->
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-6">
                                <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: progresoPago + '%' }"></div>
                            </div>

                            <!-- Botón de Pago (Cliente) -->
                            <div v-if="isClient && saldoPendiente > 0" class="space-y-4">
                                <button @click="generateQr" 
                                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                                    Pagar {{ order.numero_cuotas > 1 ? 'Cuota' : 'Total' }} con QR
                                </button>
                                <p v-if="order.numero_cuotas > 1" class="text-center text-sm text-theme-text opacity-70">
                                    Plan de Pagos: {{ order.cuotas_pagadas }} de {{ order.numero_cuotas }} cuotas pagadas.
                                </p>
                            </div>

                            <!-- Aplicar Descuento (Cliente) -->
                            <div v-if="isClient && order.monto_descuento == 0 && saldoPendiente > 0" class="mt-6 pt-6 border-t border-theme-border">
                                <label class="block text-sm font-medium text-theme-text mb-2">¿Tienes un código de descuento?</label>
                                <div class="flex space-x-2">
                                    <input v-model="formDiscount.codigo" type="text" placeholder="CÓDIGO" class="flex-1 rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white">
                                    <button @click="applyDiscount" :disabled="formDiscount.processing" class="bg-purple-600 text-white px-4 rounded-lg font-bold hover:bg-purple-700">
                                        Aplicar
                                    </button>
                                </div>
                                <p v-if="formDiscount.errors.codigo" class="text-red-500 text-xs mt-1">{{ formDiscount.errors.codigo }}</p>
                            </div>
                        </div>

                        <!-- Panel de Administración (Admin) -->
                        <div v-if="canEdit" class="bg-theme-card shadow-lg rounded-xl border border-theme-border p-6">
                            <h3 class="text-lg font-bold text-theme-text mb-4">Administrar Pedido</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Estado</label>
                                    <select v-model="formUpdate.estado" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
                                        <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Presupuesto Total (Bs)</label>
                                    <input v-model="formUpdate.presupuesto_total" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Número de Cuotas</label>
                                    <input v-model="formUpdate.numero_cuotas" type="number" min="1" max="12" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
                                </div>

                                <button @click="updateOrder" :disabled="formUpdate.processing" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
