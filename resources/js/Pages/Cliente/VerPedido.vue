<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    pedido: Object,
    qr_data: Object, // Recibir QR data desde el controlador
});

const page = usePage();
const showQrModal = ref(false);
const qrData = ref(props.qr_data || null);

// Watch for qr_data from flash messages
watch(() => page.props.flash?.qr_data, (newQrData) => {
    if (newQrData) {
        qrData.value = newQrData;
        showQrModal.value = true;
    }
}, { immediate: true });

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

const totalPagado = computed(() => {
    return props.pedido.pagos?.filter(p => p.qr_status === 'PAID').reduce((acc, curr) => acc + parseFloat(curr.monto), 0) || 0;
});

const saldoPendiente = computed(() => {
    const total = parseFloat(props.pedido.presupuesto_total) - parseFloat(props.pedido.monto_descuento || 0);
    return Math.max(0, total - totalPagado.value);
});

const progresoPago = computed(() => {
    const total = parseFloat(props.pedido.presupuesto_total) - parseFloat(props.pedido.monto_descuento || 0);
    if (total <= 0) return 0;
    return Math.min(100, (totalPagado.value / total) * 100);
});

const qrForm = useForm({
    pedido_id: props.pedido.pedido_id
});

const generateQr = () => {
    console.log('═══════════════════════════════════════════');
    console.log('🔵 Cliente: Generando QR para pedido');
    console.log('📋 Datos del pedido:', {
        pedido_id: props.pedido.pedido_id,
        presupuesto_total: props.pedido.presupuesto_total,
        monto_descuento: props.pedido.monto_descuento,
        estado: props.pedido.estado,
        pagos_count: props.pedido.pagos?.length || 0
    });
    console.log('═══════════════════════════════════════════');

    qrForm.post(route('payments.generate-qr'), {
        preserveScroll: true,
        onSuccess: (response) => {
            console.log('✅ QR Generado exitosamente');
            console.log('Response:', response);
            // El QR data viene en la respuesta flash
            const flashQrData = response.props.flash?.qr_data;
            if (flashQrData) {
                console.log('✅ QR Data recibido:', flashQrData);
                qrData.value = flashQrData;
                showQrModal.value = true;
            } else {
                console.warn('⚠️ No se recibió QR data en la respuesta');
            }
        },
        onError: (errors) => {
            console.log('═══════════════════════════════════════════');
            console.error('❌ Error al generar QR');
            console.error('Errors:', errors);
            console.log('═══════════════════════════════════════════');
            alert('Error al generar QR: ' + (errors.error || errors.pedido_id || 'Error desconocido'));
        }
    });
};

const closeQrModal = () => {
    showQrModal.value = false;
};
</script>

<template>
    <Head title="Detalle del Pedido" />

    <AuthenticatedLayout page-title="Mi Pedido">
        <div class="max-w-5xl mx-auto">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 text-white mb-8 shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Pedido #{{ pedido.pedido_id }}</h1>
                        <p class="opacity-90">Solicitado el {{ new Date(pedido.fecha_solicitud).toLocaleDateString() }}</p>
                    </div>
                    <span :class="['px-4 py-2 rounded-full text-sm font-bold', getStatusColor(pedido.estado)]">
                        {{ pedido.estado.replace('_', ' ') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna Izquierda: Detalles -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Info General -->
                    <div class="bg-theme-card overflow-hidden shadow-lg rounded-xl border border-theme-border p-6">
                        <h3 class="text-lg font-bold text-theme-text mb-4 border-b border-theme-border pb-2">Información del Pedido</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-theme-text opacity-70">Tipo de Servicio</p>
                                <p class="font-semibold text-theme-text">{{ pedido.tipo_servicio }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-theme-text opacity-70">Fecha Estimada de Entrega</p>
                                <p class="font-semibold text-theme-text">
                                    {{ pedido.fecha_entrega_estimada ? new Date(pedido.fecha_entrega_estimada).toLocaleDateString() : 'Por definir' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-theme-text opacity-70">Descripción</p>
                            <p class="bg-theme-bg p-3 rounded-lg mt-1 text-theme-text">{{ pedido.descripcion_prenda }}</p>
                        </div>
                    </div>

                    <!-- Historial de Pagos -->
                    <div class="bg-theme-card overflow-hidden shadow-lg rounded-xl border border-theme-border p-6">
                        <h3 class="text-lg font-bold text-theme-text mb-4 border-b border-theme-border pb-2">Historial de Pagos</h3>
                        <div v-if="pedido.pagos && pedido.pagos.length > 0" class="overflow-x-auto">
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
                                    <tr v-for="pago in pedido.pagos" :key="pago.pago_id">
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

                <!-- Columna Derecha: Finanzas y Acciones -->
                <div class="space-y-6">
                    <!-- Resumen Financiero -->
                    <div class="bg-theme-card shadow-lg rounded-xl border border-theme-border p-6">
                        <h3 class="text-lg font-bold text-theme-text mb-4">Resumen Financiero</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-theme-text">
                                <span>Presupuesto Total</span>
                                <span class="font-bold">Bs {{ Number(pedido.presupuesto_total).toFixed(2) }}</span>
                            </div>
                            <div v-if="pedido.monto_descuento > 0" class="flex justify-between text-green-600">
                                <span>Descuento</span>
                                <span class="font-bold">- Bs {{ Number(pedido.monto_descuento).toFixed(2) }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                            <div class="flex justify-between text-lg font-bold text-theme-text">
                                <span>Total a Pagar</span>
                                <span>Bs {{ (pedido.presupuesto_total - (pedido.monto_descuento || 0)).toFixed(2) }}</span>
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

                        <!-- Botón de Pago -->
                        <div v-if="saldoPendiente > 0" class="space-y-4">
                            <button @click="generateQr" 
                                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                                Pagar {{ pedido.numero_cuotas > 1 ? 'Cuota' : 'Total' }} con QR
                            </button>
                            <p v-if="pedido.numero_cuotas > 1" class="text-center text-sm text-theme-text opacity-70">
                                Plan de Pagos: {{ pedido.cuotas_pagadas }} de {{ pedido.numero_cuotas }} cuotas pagadas.
                            </p>
                        </div>
                        <div v-else>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-center">
                                <p class="text-green-600 dark:text-green-400 font-bold">✓ Pedido Pagado Completamente</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Volver -->
                    <Link :href="route('cliente.mis-pedidos')" 
                          class="block w-full text-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition">
                        ← Volver a Mis Pedidos
                    </Link>
                </div>
            </div>
        </div>

        <!-- Modal para mostrar QR -->
        <Modal :show="showQrModal" @close="closeQrModal">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Código QR generado</h2>
                
                <div v-if="qrData" class="text-center">
                    <!-- QR Image -->
                    <div class="bg-white p-4 rounded-lg inline-block mb-4">
                        <img :src="`data:image/png;base64,${qrData.qrBase64}`" 
                             alt="Código QR" 
                             class="w-64 h-64 mx-auto" />
                    </div>

                    <!-- QR Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                            <strong>ID Transacción:</strong> {{ qrData.transactionId }}
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                            <strong>Monto:</strong> Bs {{ qrData.monto ? Number(qrData.monto).toFixed(2) : '0.00' }}
                        </p>
                        <p v-if="qrData.cuota" class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                            <strong>Cuota:</strong> {{ qrData.cuota }} de {{ pedido.numero_cuotas }}
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>Expira:</strong> {{ new Date(qrData.expirationDate).toLocaleString() }}
                        </p>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300 font-semibold mb-2">
                            📱 Instrucciones:
                        </p>
                        <ol class="text-left text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>1. Abre tu app bancaria (ej: BCP, Banco Nacional)</li>
                            <li>2. Selecciona "Pagar con QR"</li>
                            <li>3. Escanea este código</li>
                            <li>4. Confirma el pago</li>
                        </ol>
                    </div>

                    <button @click="closeQrModal" 
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
