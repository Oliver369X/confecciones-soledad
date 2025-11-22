<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50">
        <!-- Navbar Moderno -->
        <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-2 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                            Confecciones Soledad
                        </span>
                    </div>

                    <!-- Menu -->
                    <div class="hidden md:flex items-center space-x-8">
                        <Link :href="route('public.home')" class="text-gray-700 hover:text-purple-600 transition font-medium">
                            Inicio
                        </Link>
                        <Link :href="route('public.catalogo')" class="text-gray-700 hover:text-purple-600 transition font-medium">
                            Catálogo
                        </Link>
                        <Link :href="route('public.nosotros')" class="text-gray-700 hover:text-purple-600 transition font-medium">
                            Nosotros
                        </Link>
                        <Link :href="route('public.hacer-pedido')" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition transform hover:scale-105">
                            Hacer Pedido
                        </Link>
                        
                        <div v-if="$page.props.auth.user" class="flex items-center space-x-4">
                            <Link :href="route('dashboard')" class="text-gray-700 hover:text-purple-600">
                                Dashboard
                            </Link>
                        </div>
                        <div v-else class="flex items-center space-x-4">
                            <Link :href="route('login')" class="text-gray-700 hover:text-purple-600">
                                Iniciar Sesión
                            </Link>
                            <Link :href="route('register')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-full hover:bg-gray-300 transition">
                                Registrarse
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <!-- Texto -->
                    <div class="space-y-8 animate-fade-in">
                        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight">
                            Dale vida a tu
                            <span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                estilo único
                            </span>
                        </h1>
                        <p class="text-xl text-gray-600">
                            Confecciones y arreglos de alta calidad. Transforma tu ropa en obras de arte.
                        </p>
                        <div class="flex space-x-4">
                            <Link :href="route('public.hacer-pedido')" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-2xl transition transform hover:scale-105">
                                Solicitar Servicio
                            </Link>
                            <Link :href="route('public.catalogo')" class="border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-purple-50 transition">
                                Ver Trabajos
                            </Link>
                        </div>
                    </div>

                    <!-- Imagen decorativa -->
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-blue-400 rounded-3xl transform rotate-6"></div>
                        <img src="https://images.unsplash.com/photo-1558769132-cb1aea78c3d4?w=600" 
                             alt="Confecciones" 
                             class="relative rounded-3xl shadow-2xl object-cover h-96 w-full">
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios -->
        <div class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-4xl font-bold text-center mb-12 text-gray-900">Nuestros Servicios</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 rounded-2xl hover:shadow-xl transition transform hover:-translate-y-2">
                        <div class="bg-purple-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold mb-4 text-gray-900">Confección</h3>
                        <p class="text-gray-600">Creamos prendas desde cero, adaptadas a tus medidas y estilo personal.</p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 p-8 rounded-2xl hover:shadow-xl transition transform hover:-translate-y-2">
                        <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold mb-4 text-gray-900">Arreglos</h3>
                        <p class="text-gray-600">Ajustes, reparaciones y modificaciones para que tu ropa quede perfecta.</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl hover:shadow-xl transition transform hover:-translate-y-2">
                        <div class="bg-pink-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold mb-4 text-gray-900">Calidad Garantizada</h3>
                        <p class="text-gray-600">Trabajamos con los mejores materiales y técnicas profesionales.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Portfolio Destacado -->
        <div class="bg-gradient-to-br from-gray-50 to-purple-50 py-20" v-if="portfolio && portfolio.length > 0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Trabajos Recientes</h2>
                    <p class="text-xl text-gray-600">Mira algunos de nuestros proyectos más destacados</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div v-for="item in portfolio" :key="item.portafolio_id" 
                         class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2">
                        <img :src="item.imagen_url_principal" 
                             :alt="item.titulo"
                             class="w-full h-64 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ item.titulo }}</h3>
                            <p class="text-gray-600 line-clamp-2">{{ item.descripcion }}</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <Link :href="route('public.catalogo')" class="inline-block bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-2xl transition transform hover:scale-105">
                        Ver Más Trabajos →
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">Confecciones Soledad</h3>
                        <p class="text-gray-400">Tu taller de confianza para confecciones y arreglos de calidad.</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                        <div class="space-y-2">
                            <Link :href="route('public.catalogo')" class="block text-gray-400 hover:text-white transition">Catálogo</Link>
                            <Link :href="route('public.nosotros')" class="block text-gray-400 hover:text-white transition">Nosotros</Link>
                            <Link :href="route('public.hacer-pedido')" class="block text-gray-400 hover:text-white transition">Hacer Pedido</Link>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                        <p class="text-gray-400">Email: contacto@confeccionessoledad.com</p>
                        <p class="text-gray-400">Teléfono: +591 75123456</p>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 Confecciones Soledad. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    portfolio: Array,
});
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
