<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    items: Array,
});

const form = useForm({});

const deleteItem = (id) => {
    if (confirm('¿Estás seguro de eliminar este item?')) {
        form.delete(route('portfolio.destroy', id));
    }
};
</script>

<template>
    <Head title="Portafolio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Portafolio</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium">Galería de Trabajos</h3>
                            <Link :href="route('portfolio.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Nuevo Item
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div v-for="item in items" :key="item.portafolio_id" class="border rounded-lg p-4 shadow hover:shadow-lg transition">
                                <img :src="item.imagen_url_principal" alt="Trabajo" class="w-full h-48 object-cover rounded mb-4">
                                <h4 class="font-bold text-lg">{{ item.titulo }}</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ item.descripcion }}</p>
                                <div class="flex justify-end space-x-2 mt-4">
                                    <Link :href="route('portfolio.edit', item.portafolio_id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                    <button @click="deleteItem(item.portafolio_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
