<template>
    <!-- Navbar Moderno Reutilizable -->
    <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <Link :href="route('public.home')" class="flex items-center space-x-3">
                    <Logo size="32" />
                    <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Confecciones Soledad
                    </span>
                </Link>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link :href="route('public.home')" 
                          :class="[
                              'transition font-medium',
                              isActive('public.home') ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600'
                          ]">
                        Inicio
                    </Link>
                    <Link :href="route('public.catalogo')" 
                          :class="[
                              'transition font-medium',
                              isActive('public.catalogo') ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600'
                          ]">
                        Catálogo
                    </Link>
                    <Link :href="route('public.nosotros')" 
                          :class="[
                              'transition font-medium',
                              isActive('public.nosotros') ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600'
                          ]">
                        Nosotros
                    </Link>
                    <Link :href="route('public.hacer-pedido')" 
                          class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition transform hover:scale-105 font-medium">
                        Hacer Pedido
                    </Link>
                    
                    <!-- Auth Buttons -->
                    <div v-if="$page.props.auth.user" class="flex items-center space-x-3">
                        <Link :href="route('dashboard')" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition font-medium">
                            Dashboard
                        </Link>
                    </div>
                    <div v-else class="flex items-center space-x-3">
                        <Link :href="route('login')" class="text-gray-700 hover:text-purple-600 transition font-medium px-4 py-2 rounded-full hover:bg-gray-100">
                            Iniciar Sesión
                        </Link>
                        <Link :href="route('register')" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition font-medium shadow-md">
                            Registrarse
                        </Link>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden py-4 border-t">
                <div class="flex flex-col space-y-3">
                    <Link :href="route('public.home')" class="text-gray-700 hover:text-purple-600 px-4 py-2">Inicio</Link>
                    <Link :href="route('public.catalogo')" class="text-gray-700 hover:text-purple-600 px-4 py-2">Catálogo</Link>
                    <Link :href="route('public.nosotros')" class="text-gray-700 hover:text-purple-600 px-4 py-2">Nosotros</Link>
                    <Link :href="route('public.hacer-pedido')" class="text-gray-700 hover:text-purple-600 px-4 py-2">Hacer Pedido</Link>
                    <div v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="bg-purple-600 text-white px-4 py-2 rounded-lg mx-4 block text-center">Dashboard</Link>
                    </div>
                    <div v-else class="px-4 space-y-2">
                        <Link :href="route('login')" class="block text-center border border-purple-600 text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50">Iniciar Sesión</Link>
                        <Link :href="route('register')" class="block text-center bg-purple-600 text-white px-4 py-2 rounded-lg">Registrarse</Link>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Logo from '@/Components/Logo.vue';

const page = usePage();
const mobileMenuOpen = ref(false);

const isActive = (routeName) => {
    return page.url.startsWith('/' + routeName.replace('public.', '').replace('.', '/'));
};
</script>
