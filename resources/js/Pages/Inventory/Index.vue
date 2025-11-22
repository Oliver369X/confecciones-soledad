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
    items: Array,
});

const showCreateModal = ref(false);
const showMovementModal = ref(false);
const selectedItem = ref(null);

const createForm = useForm({
    nombre_material: '',
    descripcion: '',
    cantidad_stock: 0,
    unidad_medida: '',
    costo_unitario: 0,
});

const movementForm = useForm({
    tipo_movimiento: 'ENTRADA',
    cantidad: 0,
    motivo: '',
});

const openCreateModal = () => {
    createForm.reset();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.reset();
};

const submitCreate = () => {
    createForm.post(route('inventory.store'), {
        onSuccess: () => closeCreateModal(),
    });
};

const openMovementModal = (item) => {
    selectedItem.value = item;
    movementForm.reset();
    showMovementModal.value = true;
};

const closeMovementModal = () => {
    showMovementModal.value = false;
    selectedItem.value = null;
    movementForm.reset();
};

const submitMovement = () => {
    movementForm.post(route('inventory.movement.store', selectedItem.value.item_id), {
        onSuccess: () => closeMovementModal(),
    });
};

const deleteItem = (id) => {
    if (confirm('¿Estás seguro de eliminar este material?')) {
        useForm({}).delete(route('inventory.destroy', id));
    }
};
</script>

<template>
    <Head title="Inventario" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestión de Inventarios</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium">Materiales e Insumos</h3>
                            <PrimaryButton @click="openCreateModal">
                                Nuevo Material
                            </PrimaryButton>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Material</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidad</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Costo Unit.</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="item in items" :key="item.item_id">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ item.nombre_material }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ item.descripcion }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold" :class="{'text-red-600': item.cantidad_stock <= 5, 'text-green-600': item.cantidad_stock > 5}">
                                            {{ item.cantidad_stock }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ item.unidad_medida }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Bs {{ item.costo_unitario }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openMovementModal(item)" class="text-indigo-600 hover:text-indigo-900 mr-4">Movimiento</button>
                                            <button @click="deleteItem(item.item_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear Material -->
        <Modal :show="showCreateModal" @close="closeCreateModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registrar Nuevo Material</h2>
                <div class="mt-6">
                    <div>
                        <InputLabel for="nombre_material" value="Nombre del Material" />
                        <TextInput id="nombre_material" v-model="createForm.nombre_material" type="text" class="mt-1 block w-full" required autofocus />
                        <InputError :message="createForm.errors.nombre_material" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="descripcion" value="Descripción" />
                        <TextInput id="descripcion" v-model="createForm.descripcion" type="text" class="mt-1 block w-full" />
                        <InputError :message="createForm.errors.descripcion" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="cantidad_stock" value="Stock Inicial" />
                        <TextInput id="cantidad_stock" v-model="createForm.cantidad_stock" type="number" step="0.01" class="mt-1 block w-full" required />
                        <InputError :message="createForm.errors.cantidad_stock" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="unidad_medida" value="Unidad de Medida" />
                        <TextInput id="unidad_medida" v-model="createForm.unidad_medida" type="text" class="mt-1 block w-full" required placeholder="Ej: Metros, Unidades" />
                        <InputError :message="createForm.errors.unidad_medida" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="costo_unitario" value="Costo Unitario (Bs)" />
                        <TextInput id="costo_unitario" v-model="createForm.costo_unitario" type="number" step="0.01" class="mt-1 block w-full" required />
                        <InputError :message="createForm.errors.costo_unitario" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeCreateModal">Cancelar</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': createForm.processing }" :disabled="createForm.processing" @click="submitCreate">
                        Guardar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal Movimiento Inventario -->
        <Modal :show="showMovementModal" @close="closeMovementModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registrar Movimiento: {{ selectedItem?.nombre_material }}</h2>
                <div class="mt-6">
                    <div>
                        <InputLabel for="tipo_movimiento" value="Tipo de Movimiento" />
                        <select id="tipo_movimiento" v-model="movementForm.tipo_movimiento" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="ENTRADA">Entrada (Compra/Devolución)</option>
                            <option value="SALIDA">Salida (Uso/Pérdida)</option>
                        </select>
                        <InputError :message="movementForm.errors.tipo_movimiento" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="cantidad" value="Cantidad" />
                        <TextInput id="cantidad" v-model="movementForm.cantidad" type="number" step="0.01" class="mt-1 block w-full" required />
                        <InputError :message="movementForm.errors.cantidad" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="motivo" value="Motivo" />
                        <TextInput id="motivo" v-model="movementForm.motivo" type="text" class="mt-1 block w-full" required placeholder="Ej: Compra de material, Uso en pedido #123" />
                        <InputError :message="movementForm.errors.motivo" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeMovementModal">Cancelar</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': movementForm.processing }" :disabled="movementForm.processing" @click="submitMovement">
                        Registrar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
