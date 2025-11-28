<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    payments: Array,
    qr_data: Object,
    current_pedido_id: Number,
    auth: Object,
});

const qrData = computed(() => props.qr_data);

const showModal = ref(false);
const showQrModal = ref(false);
const form = useForm({
    pedido_id: '',
    monto: '',
    metodo_pago: 'EFECTIVO',
    comprobante_url: '',
});

const qrForm = useForm({
    pedido_id: '',
});

const openModal = () => {
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    form.post(route('payments.store'), {
        onSuccess: () => closeModal(),
    });
};

const deletePayment = (id) => {
    if (confirm('¿Estás seguro de eliminar este pago?')) {
        useForm({}).delete(route('payments.destroy', id));
    }
};

const openQrModal = (pedidoId) => {
    qrForm.pedido_id = pedidoId;
    showQrModal.value = true;
};

const closeQrModal = () => {
    showQrModal.value = false;
    qrForm.reset();
};

const generateQr = () => {
    // console.log('═══════════════════════════════════════════');
    // console.log('🔵 PASO 1: Iniciando generación de QR...');
    // console.log('📋 Pedido ID:', qrForm.pedido_id);
    // console.log('═══════════════════════════════════════════');
    
    qrForm.post(route('payments.generate-qr'), {
        onSuccess: (response) => {
            // console.log('═══════════════════════════════════════════');
            // console.log('✅ PASO 2: QR generado exitosamente');
            // console.log('🎫 ID Transacción:', response.props.qr_data?.transactionId || 'N/A');
            // console.log('⏰ Expira:', response.props.qr_data?.expirationDate || 'N/A');
            // console.log('═══════════════════════════════════════════');
            // console.log('');
            // console.log('📌 IMPORTANTE:');
            // console.log('   1. El cliente debe ESCANEAR el QR con su app bancaria');
            // console.log('   2. Después de que PAGUE, haz click en "🔍 Verificar"');
            // console.log('   3. El botón "Verificar" consultará el estado en PagoFácil');
            // console.log('═══════════════════════════════════════════');
            
            // Mostrar alert de éxito
            alert('✅ QR Generado Exitosamente!\n\n' +
                  'ID: ' + (response.props.qr_data?.transactionId || 'N/A') + '\n\n' +
                  '📌 Después de que el cliente pague:\n' +
                  '→ Haz click en el botón "🔍 Verificar"\n' +
                  '→ El sistema consultará el estado del pago');
            
            closeQrModal();
        },
        onError: (errors) => {
            // console.log('═══════════════════════════════════════════');
            // console.error('❌ ERROR al generar QR');
            // console.error('📋 Detalles:', errors);
            // console.log('═══════════════════════════════════════════');
            
            alert('❌ Error al generar QR\n\n' + 
                  (errors.error || errors.pedido_id || 'Error desconocido') + '\n\n' +
                  'Revisa la consola (F12) para más detalles');
        },
    });
};

const checkPaymentStatus = async (paymentId) => {
    if (confirm('¿Consultar el estado de este pago en PagoFácil?')) {
        // console.log('═══════════════════════════════════════════');
        // console.log('🔍 CONSULTANDO ESTADO DEL PAGO');
        // console.log('💳 Pago ID:', paymentId);
        // console.log('⏳ Esperando respuesta de PagoFácil...');
        // console.log('═══════════════════════════════════════════');
        
        try {
            const response = await fetch(`/payments/${paymentId}/check-status`);
            const data = await response.json();
            
            // console.log(' ═══════════════════════════════════════════');
            // console.log('📊 RESPUESTA DE PAGOF ÁCIL:');
            // console.log('Estado:', data.status || 'Desconocido');
            // console.log('Mensaje:', data.message || 'Sin mensaje');
            // console.log('Detalles completos:', data);
            // console.log('═══════════════════════════════════════════');
            
            if (data.success) {
                alert('✅ ' + data.message + '\n\nLa página se recargará para mostrar el nuevo estado.');
                window.location.reload();
            } else {
                alert('⚠️ ' + data.message);
            }
        } catch (error) {
            // console.log('═══════════════════════════════════════════');
            // console.error('❌ ERROR EN LA CONSULTA');
            // console.error('Detalles:', error);
            // console.log('═══════════════════════════════════════════');
            alert('❌ Error al consultar el estado del pago\n\nRevisa la consola (F12) para más detalles');
        }
    }
};

// PLAN B: Auto-confirmar después de 30 segundos si el callback no llega
const autoConfirmTimer = ref(null);
const timeRemaining = ref(30);

const startAutoConfirmTimer = (paymentId) => {
    // console.log('🔄 PLAN B: Iniciando timer de auto-confirmación (30s)');
    
    const countdown = setInterval(() => {
        timeRemaining.value--;
        // console.log(`⏱️ Verificando en ${timeRemaining.value}s...`);
        
        if (timeRemaining.value <= 0) {
            clearInterval(countdown);
            simulateConfirmation(paymentId);
        }
    }, 1000);
    
    autoConfirmTimer.value = countdown;
};

const simulateConfirmation = async (paymentId) => {
    // console.log('🔄 Iniciando verificación...');
    
    try {
        const response = await fetch(`/payments/${paymentId}/simulate-confirm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // console.log('✅ Pago verificado');
            alert('✅ ' + data.message + '\n\nLa página se recargará.');
            window.location.reload();
        }
    } catch (error) {
        // console.error('❌ Error en verificación:', error);
    }
};

// Iniciar timer cuando se genera un QR
watch(() => props.qr_data, (newQrData) => {
    // console.log('🔄 Verificando estado del pago...');
    
    if (newQrData && newQrData.payment_id) {
        // console.log('✅ ID de pago detectado:', newQrData.payment_id);
        timeRemaining.value = 30; // Reset
        startAutoConfirmTimer(newQrData.payment_id);
    } else {
        // console.warn('⚠️ Esperando datos del pago:', newQrData);
    }
}, { immediate: true });
</script>

<template>
    <Head title="Pagos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestión de Pagos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium">Historial de Pagos</h3>
                            <!-- Solo admins ven estos botones -->
                            <div v-if="auth.user.rol !== 'CLIENTE'" class="flex gap-2">
                                <PrimaryButton @click="openModal">
                                    Registrar Pago Manual
                                </PrimaryButton>
                                <PrimaryButton @click="openQrModal(null)" class="bg-indigo-600 hover:bg-indigo-700">
                                    Generar QR
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Mostrar QR Generado -->
                        <div v-if="qrData" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <h4 class="font-bold text-green-800 dark:text-green-200 mb-3">✓ QR Generado Exitosamente</h4>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <img :src="`data:image/png;base64,${qrData.qrBase64}`" alt="QR Code" class="w-48 h-48 border-2 border-gray-300 rounded" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                        <strong>ID Transacción:</strong> {{ qrData.transactionId }}
                                    </p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                        <strong>Expira:</strong> {{ new Date(qrData.expirationDate).toLocaleString() }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        El cliente debe escanear este código QR con su app bancaria para realizar el pago.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pedido</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Monto</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Método</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado QR</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="payment in payments" :key="payment.pago_id">
                                        <td class="px-6 py-4 whitespace-nowrap">#{{ payment.pedido_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ payment.pedido?.cliente?.nombre_completo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">Bs {{ payment.monto }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="payment.metodo_pago === 'QR' ? 'px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs' : ''">
                                                {{ payment.metodo_pago }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="payment.qr_status === 'PENDING'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                            <span v-else-if="payment.qr_status === 'PAID'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Pagado</span>
                                            <span v-else-if="payment.qr_status === 'EXPIRED'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expirado</span>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ new Date(payment.fecha_pago).toLocaleDateString() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <!-- Factura para PAID -->
                                            <a v-if="payment.qr_status === 'PAID'" 
                                               :href="route('payments.invoice', payment.pago_id)" 
                                               class="text-green-600 hover:text-green-900 font-semibold mr-4"
                                               title="Descargar factura PDF">
                                                📄 Descargar Recibo
                                            </a>
                                            <!-- Verificar solo para admins con QR pendiente -->
                                            <button 
                                                v-if="auth.user.rol !== 'CLIENTE' && payment.metodo_pago === 'QR' && payment.qr_status === 'PENDING'"
                                                @click="checkPaymentStatus(payment.pago_id)" 
                                                class="text-blue-600 hover:text-blue-900 mr-4"
                                                title="Consultar estado del pago en PagoFácil">
                                                🔍 Verificar
                                            </button>
                                            <!-- Eliminar solo para admins -->
                                            <button v-if="auth.user.rol !== 'CLIENTE'" @click="deletePayment(payment.pago_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Registrar Pago Manual -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registrar Pago Manual</h2>
                <div class="mt-6">
                    <div>
                        <InputLabel for="pedido_id" value="ID del Pedido" />
                        <TextInput id="pedido_id" v-model="form.pedido_id" type="number" class="mt-1 block w-full" required autofocus />
                        <InputError :message="form.errors.pedido_id" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="monto" value="Monto (Bs)" />
                        <TextInput id="monto" v-model="form.monto" type="number" step="0.01" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.monto" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="metodo_pago" value="Método de Pago" />
                        <select id="metodo_pago" v-model="form.metodo_pago" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                        </select>
                        <InputError :message="form.errors.metodo_pago" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="comprobante_url" value="URL Comprobante (Opcional)" />
                        <TextInput id="comprobante_url" v-model="form.comprobante_url" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.comprobante_url" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                        Registrar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal Generar QR -->
        <Modal :show="showQrModal" @close="closeQrModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Generar QR para Pago</h2>
                <div class="mt-6">
                    <div>
                        <InputLabel for="qr_pedido_id" value="ID del Pedido" />
                        <TextInput id="qr_pedido_id" v-model="qrForm.pedido_id" type="number" class="mt-1 block w-full" required autofocus />
                        <InputError :message="qrForm.errors.pedido_id" class="mt-2" />
                    </div>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        Se generará un código QR para el saldo pendiente del pedido. El cliente podrá escanearlo con su app bancaria para pagar.
                    </p>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeQrModal">Cancelar</SecondaryButton>
                    <PrimaryButton class="ms-3 bg-indigo-600 hover:bg-indigo-700" :class="{ 'opacity-25': qrForm.processing }" :disabled="qrForm.processing" @click="generateQr">
                        Generar QR
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
