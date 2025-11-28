<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    portfolio: Object,
});

const verDetalle = (item) => {
    // Implementar modal o navegación a detalle si es necesario
    alert(`${item.titulo}\n\n${item.descripcion}`);
};
</script>

<template>
    <Head title="Catálogo" />

    <AuthenticatedLayout page-title="Catálogo">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 text-white mb-8 shadow-lg">
            <h1 class="text-3xl font-bold mb-2">Nuestro Catálogo</h1>
            <p class="opacity-90">Explora nuestros trabajos más destacados</p>
        </div>

        <!-- Catálogo Grid -->
        <div v-if="portfolio.data && portfolio.data.length > 0" class="grid md:grid-cols-3 gap-8">
            <div v-for="item in portfolio.data" :key="item.portafolio_id" 
                 class="bg-theme-card rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 duration-300 border border-theme-border">
                
                <!-- Imagen Principal -->
                <div class="relative group overflow-hidden h-64">
                    <img :src="item.imagen_url_principal" 
                         :alt="item.titulo"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <button @click="verDetalle(item)" class="bg-white text-purple-600 px-6 py-2 rounded-full font-semibold hover:bg-purple-50 transition">
                            Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-theme-text mb-2">{{ item.titulo }}</h3>
                    <p class="text-theme-text opacity-70 mb-4 line-clamp-3">{{ item.descripcion }}</p>
                    
                    <!-- Galería Antes/Después -->
                    <div v-if="item.imagen_url_antes && item.imagen_url_despues" class="grid grid-cols-2 gap-2 mb-4">
                        <div class="relative group">
                            <img :src="item.imagen_url_antes" class="w-full h-24 object-cover rounded-lg">
                            <span class="absolute bottom-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded">Antes</span>
                        </div>
                        <div class="relative group">
                            <img :src="item.imagen_url_despues" class="w-full h-24 object-cover rounded-lg">
                            <span class="absolute bottom-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded">Después</span>
                        </div>
                    </div>

                    <Link :href="route('public.hacer-pedido')" class="block w-full text-center bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                        Solicitar Similar
                    </Link>
                </div>
            </div>
        </div>

        <!-- Sin Resultados -->
        <div v-else class="text-center py-20 bg-theme-card rounded-2xl border border-theme-border">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="text-2xl font-semibold text-theme-text mb-2">Aún no hay trabajos publicados</h3>
            <p class="text-theme-text opacity-60 mb-6">Estamos trabajando en nuevos proyectos increíbles</p>
            <Link :href="route('public.hacer-pedido')" class="inline-block bg-primary text-white px-10 py-4 rounded-full text-lg font-bold hover:opacity-90 transition transform hover:scale-105">
                Solicitar Servicio Ahora →
            </Link>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
