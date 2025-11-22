<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50">
        <!-- Navbar (compartido) -->
        <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link :href="route('public.home')" class="flex items-center space-x-3">
                        <Logo size="40" />
                        <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                            Confecciones Soledad
                        </span>
                    </Link>

                    <div class="hidden md:flex items-center space-x-8">
                        <Link :href="route('public.home')" class="text-gray-700 hover:text-purple-600 transition font-medium">Inicio</Link>
                        <Link :href="route('public.catalogo')" class="text-gray-700 hover:text-purple-600 transition font-medium">Catálogo</Link>
                        <Link :href="route('public.nosotros')" class="text-purple-600 font-semibold">Nosotros</Link>
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
                <h1 class="text-5xl font-bold text-white mb-4">Sobre Nosotros</h1>
                <p class="text-xl text-purple-100">Tu taller de confianza desde hace más de 15 años</p>
            </div>
        </div>

        <!-- Contenido -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Historia -->
            <div class="bg-white rounded-2xl shadow-lg p-10 mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">📖 Nuestra Historia</h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                    <strong>Confecciones Soledad</strong> nació en 2009 con el sueño de la señora Soledad Ramírez, 
                    una apasionada costurera que transformó su amor por la moda en un negocio familiar.
                </p>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                    Durante más de <strong>15 años</strong>, hemos servido a cientos de familias en nuestra comunidad, 
                    ofreciendo servicios de confección y arreglos de alta calidad con un toque personal y profesional.
                </p>
                <p class="text-gray-700 text-lg leading-relaxed">
                    Hoy, seguimos creciendo gracias a la confianza de nuestros clientes y al compromiso de nuestro equipo.
                </p>
            </div>

            <!-- Misión y Visión -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8">
                    <h3 class="text-2xl font-bold text-purple-900 mb-4">🎯 Nuestra Misión</h3>
                    <p class="text-purple-800">
                        Ofrecer servicios de confección y arreglos de prendas con calidad excepcional, 
                        atención personalizada y precios justos, superando las expectativas de nuestros clientes.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                    <h3 class="text-2xl font-bold text-blue-900 mb-4">🌟 Nuestra Visión</h3>
                    <p class="text-blue-800">
                        Ser el taller de confecciones más reconocido de la región, 
                        expandiendo nuestros servicios y manteniendo siempre la calidez y profesionalismo 
                        que nos caracterizan.
                    </p>
                </div>
            </div>

            <!-- Valores -->
            <div class="bg-white rounded-2xl shadow-lg p-10 mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">💎 Nuestros Valores</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="flex items-start space-x-3">
                        <span class="text-3xl">✨</span>
                        <div>
                            <h4 class="font-semibold text-lg text-gray-900">Calidad</h4>
                            <p class="text-gray-600">Trabajamos con los mejores materiales y técnicas</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="text-3xl">🤝</span>
                        <div>
                            <h4 class="font-semibold text-lg text-gray-900">Confianza</h4>
                            <p class="text-gray-600">Tu satisfacción es nuestra prioridad</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="text-3xl">⏰</span>
                        <div>
                            <h4 class="font-semibold text-lg text-gray-900">Puntualidad</h4>
                            <p class="text-gray-600">Cumplimos con los plazos establecidos</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <span class="text-3xl">❤️</span>
                        <div>
                            <h4 class="font-semibold text-lg text-gray-900">Pasión</h4>
                            <p class="text-gray-600">Amamos lo que hacemos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipo -->
            <div class="bg-white rounded-2xl shadow-lg p-10 mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">👥 Nuestro Equipo</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-4xl text-white">👩‍💼</span>
                        </div>
                        <h4 class="font-bold text-gray-900">Soledad Ramírez</h4>
                        <p class="text-gray-600 text-sm">Fundadora y Maestra Costurera</p>
                    </div>
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-4xl text-white">👨‍🔧</span>
                        </div>
                        <h4 class="font-bold text-gray-900">Carlos Martínez</h4>
                        <p class="text-gray-600 text-sm">Especialista en Trajes</p>
                    </div>
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-pink-400 to-pink-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-4xl text-white">👩‍🎨</span>
                        </div>
                        <h4 class="font-bold text-gray-900">María López</h4>
                        <p class="text-gray-600 text-sm">Diseñadora y Bordadora</p>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-10 text-white text-center">
                <h2 class="text-3xl font-bold mb-6">📞 Contáctanos</h2>
                <div class="max-w-2xl mx-auto space-y-3">
                    <p class="text-xl">📍 Av. Principal #123, Santa Cruz, Bolivia</p>
                    <p class="text-xl">📱 WhatsApp: +591 75123456</p>
                    <p class="text-xl">✉️ contacto@confeccionessoledad.com</p>
                    <div class="pt-6">
                        <Link :href="route('public.hacer-pedido')" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-full font-bold hover:bg-purple-50 transition">
                            Hacer un Pedido →
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visit Counter -->
        <VisitCounter />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Logo from '@/Components/Logo.vue';
import VisitCounter from '@/Components/VisitCounter.vue';
</script>
