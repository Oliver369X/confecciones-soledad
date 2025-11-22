<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    promotions: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    codigo: '',
    descripcion: '',
    tipo_descuento: 'PORCENTAJE',
    valor_descuento: 0,
    fecha_inicio: '',
    fecha_fin: '',
    activa: true,
});

const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.activa = true;
    showModal.value = true;
};

const openEditModal = (promo) => {
    isEditing.value = true;
    editingId.value = promo.promocion_id;
    form.codigo = promo.codigo;
    form.descripcion = promo.descripcion;
    form.tipo_descuento = promo.tipo_descuento;
    form.valor_descuento = promo.valor_descuento;
    form.fecha_inicio = promo.fecha_inicio.split('T')[0]; // Ajuste para input date
    form.fecha_fin = promo.fecha_fin.split('T')[0];
    form.activa = Boolean(promo.activa);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('promotions.update', editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('promotions.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deletePromo = (id) => {
    if (confirm('¿Estás seguro de eliminar esta promoción?')) {
        useForm({}).delete(route('promotions.destroy', id));
    }
};
</script>

<template>
    <Head title="Promociones" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestión de Promociones</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium">Promociones Vigentes e Históricas</h3>
                            <PrimaryButton @click="openCreateModal">
                                Nueva Promoción
                            </PrimaryButton>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descuento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vigencia</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="promo in promotions" :key="promo.promocion_id">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ promo.codigo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ promo.tipo_descuento === 'PORCENTAJE' ? promo.valor_descuento + '%' : 'Bs ' + promo.valor_descuento }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ new Date(promo.fecha_inicio).toLocaleDateString() }} - {{ new Date(promo.fecha_fin).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="promo.activa" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal(promo)" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</button>
                                            <button @click="deletePromo(promo.promocion_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear/Editar Promoción -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditing ? 'Editar Promoción' : 'Nueva Promoción' }}
                </h2>
                <div class="mt-6">
                    <div>
                        <InputLabel for="codigo" value="Código Promocional" />
                        <TextInput id="codigo" v-model="form.codigo" type="text" class="mt-1 block w-full uppercase" required autofocus />
                        <InputError :message="form.errors.codigo" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="descripcion" value="Descripción" />
                        <TextInput id="descripcion" v-model="form.descripcion" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.descripcion" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="tipo_descuento" value="Tipo Descuento" />
                            <select id="tipo_descuento" v-model="form.tipo_descuento" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="PORCENTAJE">Porcentaje (%)</option>
                                <option value="MONTO_FIJO">Monto Fijo (Bs)</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="valor_descuento" value="Valor" />
                            <TextInput id="valor_descuento" v-model="form.valor_descuento" type="number" step="0.01" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <InputError :message="form.errors.valor_descuento" class="mt-2" />

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="fecha_inicio" value="Fecha Inicio" />
                            <TextInput id="fecha_inicio" v-model="form.fecha_inicio" type="date" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <InputLabel for="fecha_fin" value="Fecha Fin" />
                            <TextInput id="fecha_fin" v-model="form.fecha_fin" type="date" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <InputError :message="form.errors.fecha_fin" class="mt-2" />

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.activa" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Promoción Activa</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                        {{ isEditing ? 'Actualizar' : 'Guardar' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
