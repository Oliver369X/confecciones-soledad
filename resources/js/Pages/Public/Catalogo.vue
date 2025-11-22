<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50">
        <!-- Navbar (mismo que Home) -->
        <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-3">
                        <Link :href="route('public.home')" class="flex items-center space-x-3">
                            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-2 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                Confecciones Soledad
                            </span>
                        </Link>
                    </div>

                    <div class="hidden md:flex items-center space-x-8">
                        <Link :href="route('public.home')" class="text-gray-700 hover:text-purple-600 transition font-medium">Inicio</Link>
                        <Link :href="route('public.catalogo')" class="text-purple-600 font-semibold">Catálogo</Link>
                        <Link :href="route('public.nosotros')" class="text-gray-700 hover:text-purple-600 transition font-medium">Nosotros</Link>
                        <Link :href="route('public.hacer-pedido')" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">
                            Hacer Pedido
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-5xl font-bold text-white mb-4">Nuestro Catálogo</h1>
                <p class="text-xl text-purple-100">Explora nuestros trabajos más destacados</p>
            </div>
        </div>

        <!-- Catálogo Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div v-if="portfolio.data && portfolio.data.length > 0" class="grid md:grid-cols-3 gap-8">
                <div v-for="item in portfolio.data" :key="item.portafolio_id" 
                     class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 duration-300">
                    
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
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ item.titulo }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-3">{{ item.descripcion }}</p>
                        
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
            <div v-else class="text-center py-20">
                <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">Aún no hay trabajos publicados</h3>
                <p class="text-gray-600 mb-6">Estamos trabajando en nuevos proyectos increíbles</p>
                <Link :href="route('public.hacer-pedido')" class="inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:shadow-lg transition">
                    Hacer un Pedido
                </Link>
            </div>

            <!-- Paginación -->
            <div v-if="portfolio.links && portfolio.links.length > 3" class="flex justify-center space-x-2 mt-12">
                <Link v-for="link in portfolio.links" :key="link.label"
                      :href="link.url"
                      :class="[
                          'px-4 py-2 rounded-lg font-semibold transition',
                          link.active 
                              ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white' 
                              : 'bg-white text-gray-700 hover:bg-gray-100'
                      ]"
                      v-html="link.label">
                </Link>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 py-20">
            <div class="max-w-4xl mx-auto text-center px-4">
                <h2 class="text-4xl font-bold text-white mb-6">¿Te gustan nuestros trabajos?</h2>
                <p class="text-xl text-purple-100 mb-8">Haz realidad tu proyecto con nosotros</p>
                <Link :href="route('public.hacer-pedido')" class="inline-block bg-white text-purple-600 px-10 py-4 rounded-full text-lg font-bold hover:bg-purple-50 transition transform hover:scale-105">
                    Solicitar Servicio Ahora →
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    portfolio: Object,
});

const verDetalle = (item) => {
    // Podrías abrir un modal con más detalles
    alert(`${item.titulo}\n\n${item.descripcion}`);
};
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
