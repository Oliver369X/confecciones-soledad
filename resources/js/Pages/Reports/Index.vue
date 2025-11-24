<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    filters: Object,
    stats: Object,
});

const form = useForm({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
});

const submit = () => {
    form.get(route('reports.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Reportes y Estadísticas</h2>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md hover:opacity-80 transition text-sm font-medium">
                    🖨️ Imprimir Reporte
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="flex items-end gap-4">
                            <div>
                                <InputLabel for="start_date" value="Fecha Inicio" />
                                <TextInput id="start_date" v-model="form.start_date" type="date" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <InputLabel for="end_date" value="Fecha Fin" />
                                <TextInput id="end_date" v-model="form.end_date" type="date" class="mt-1 block w-full" required />
                            </div>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Filtrar
                            </PrimaryButton>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Ingresos -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Ingresos Totales</h3>
                            <p class="text-3xl font-bold text-green-600 mt-2">Bs {{ Number(stats.ingresos || 0).toFixed(2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Pagos confirmados en el periodo</p>
                        </div>
                    </div>

                    <!-- Costos -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Costos de Insumos</h3>
                            <p class="text-3xl font-bold text-red-600 mt-2">Bs {{ Number(stats.costos || 0).toFixed(2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Materiales utilizados en el periodo</p>
                        </div>
                    </div>

                    <!-- Rentabilidad -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Rentabilidad Neta</h3>
                            <p class="text-3xl font-bold mt-2" :class="stats.rentabilidad >= 0 ? 'text-indigo-600' : 'text-red-600'">
                                Bs {{ Number(stats.rentabilidad || 0).toFixed(2) }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">Ingresos - Costos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
