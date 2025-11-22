<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    clients: Array,
    promotions: Array,
});

const form = useForm({
    cliente_id: '',
    tipo_servicio: 'ARREGLO',
    descripcion_prenda: '',
    presupuesto_total: '',
    promocion_id: '',
});

const submit = () => {
    form.post(route('orders.store'));
};
</script>

<template>
    <Head title="Nuevo Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nuevo Pedido</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="cliente_id" value="Cliente" />
                                <select
                                    id="cliente_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.cliente_id"
                                    required
                                >
                                    <option value="" disabled>Seleccione un cliente</option>
                                    <option v-for="client in clients" :key="client.usuario_id" :value="client.usuario_id">
                                        {{ client.nombre_completo }} ({{ client.email }})
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.cliente_id" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="tipo_servicio" value="Tipo de Servicio" />
                                <select
                                    id="tipo_servicio"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.tipo_servicio"
                                    required
                                >
                                    <option value="ARREGLO">Arreglo</option>
                                    <option value="CONFECCION">Confección</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tipo_servicio" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="descripcion_prenda" value="Descripción de la Prenda" />
                                <textarea
                                    id="descripcion_prenda"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.descripcion_prenda"
                                    required
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.descripcion_prenda" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="presupuesto_total" value="Presupuesto Total (Bs)" />
                                <TextInput
                                    id="presupuesto_total"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.presupuesto_total"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.presupuesto_total" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="promocion_id" value="Promoción (Opcional)" />
                                <select
                                    id="promocion_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.promocion_id"
                                >
                                    <option value="">Ninguna</option>
                                    <option v-for="promo in promotions" :key="promo.promocion_id" :value="promo.promocion_id">
                                        {{ promo.codigo }} - {{ promo.tipo_descuento === 'PORCENTAJE' ? promo.valor_descuento + '%' : 'Bs ' + promo.valor_descuento }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.promocion_id" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('orders.index')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                    Cancelar
                                </Link>
                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Crear Pedido
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
