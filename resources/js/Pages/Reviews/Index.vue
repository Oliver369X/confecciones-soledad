<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    reviews: Array,
});

const getStars = (rating) => {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
};
</script>

<template>
    <Head title="Reseñas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Reseñas de Clientes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="review in reviews" :key="review.resena_id" class="border rounded-lg p-4 shadow bg-gray-50 dark:bg-gray-700">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-lg">{{ review.cliente?.nombre_completo }}</div>
                                    <div class="text-yellow-500 text-lg">{{ getStars(review.calificacion) }}</div>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Pedido #{{ review.pedido_id }} - {{ new Date(review.fecha_resena).toLocaleDateString() }}</p>
                                <p class="text-gray-700 dark:text-gray-300 italic">"{{ review.comentario }}"</p>
                            </div>
                        </div>

                        <div v-if="reviews.length === 0" class="text-center text-gray-500 dark:text-gray-400 mt-8">
                            No hay reseñas registradas aún.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
