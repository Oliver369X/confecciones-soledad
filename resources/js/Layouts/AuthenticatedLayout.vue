<template>
    <div class="min-h-screen bg-theme-bg text-theme-text transition-colors duration-300">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-purple-900 to-purple-700 shadow-2xl z-30">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 border-b border-purple-600">
                <Link :href="route('dashboard')" class="flex items-center space-x-3">
                    <Logo size="40" class="text-white" />
                    <span class="text-xl font-bold text-white">
                        Confecciones<br/>Soledad
                    </span>
                </Link>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b border-purple-600">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold">
                        {{ userInitials }}
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ $page.props.auth.user.nombre_completo }}</p>
                        <p class="text-purple-200 text-xs">{{ roleName }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="mt-4 px-2">
                <div v-for="item in menuItems" :key="item.route" class="mb-1">
                    <NavLink 
                        :href="route(item.route)" 
                        :active="route().current(item.route + '*')"
                        :icon="item.icon"
                    >
                        {{ item.label }}
                    </NavLink>
                </div>
            </nav>

            <!-- Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-purple-600">
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="w-full flex items-center space-x-3 px-4 py-3 text-white hover:bg-purple-600 rounded-lg transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Cerrar Sesión</span>
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-64 transition-all duration-300">
            <!-- Top Bar -->
            <header class="bg-theme-card shadow-sm border-b border-theme-border">
                <div class="px-6 py-4 flex items-center justify-between">
                    <!-- Breadcrumb -->
                    <div class="flex items-center space-x-2 text-sm text-theme-text opacity-70">
                        <Link :href="route('dashboard')" class="hover:text-primary">Dashboard</Link>
                        <span v-if="pageTitle">/</span>
                        <span v-if="pageTitle" class="font-semibold text-theme-text">{{ pageTitle }}</span>
                    </div>

                    <!-- Theme & Accessibility -->
                    <div class="flex items-center space-x-4">
                        <ThemeSelector />
                        <span class="text-sm text-theme-text opacity-70">{{ currentTime }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>

            <!-- Visit Counter -->
            <VisitCounter />
        </div>

        <!-- Accessibility Panel (Global) -->
        <AccessibilityPanel />
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import Logo from '@/Components/Logo.vue';
import NavLink from '@/Components/NavLink.vue';
import ThemeSelector from '@/Components/ThemeSelector.vue';
import AccessibilityPanel from '@/Components/AccessibilityPanel.vue';
import VisitCounter from '@/Components/VisitCounter.vue';

const page = usePage();

defineProps({
    pageTitle: {
        type: String,
        default: null
    }
});

// User Info
const userInitials = computed(() => {
    const name = page.props.auth.user.nombre_completo || '';
    const parts = name.split(' ');
    return parts.length > 1 
        ? (parts[0][0] + parts[1][0]).toUpperCase()
        : name.substring(0, 2).toUpperCase();
});

const roleName = computed(() => {
    const rol = page.props.auth.user.rol;
    return {
        'PROPIETARIO': '👑 Propietario',
        'AYUDANTE': '🛠️ Ayudante',
        'CLIENTE': '👤 Cliente'
    }[rol] || rol;
});

// Dynamic Menu Based on Role
const menuItems = computed(() => {
    const user = page.props.auth.user;
    const rol = user.rol;

    // Menu completo para PROPIETARIO
    const fullMenu = [
        { route: 'dashboard', label: 'Dashboard', icon: 'home' },
        { route: 'users.index', label: 'Usuarios', icon: 'users' },
        { route: 'orders.index', label: 'Pedidos', icon: 'clipboard' },
        { route: 'portfolio.index', label: 'Portafolio', icon: 'image' },
        { route: 'inventory.index', label: 'Inventario', icon: 'box' },
        { route: 'payments.index', label: 'Pagos', icon: 'credit-card' },
        { route: 'promotions.index', label: 'Promociones', icon: 'tag' },
        { route: 'reviews.index', label: 'Reseñas', icon: 'star' },
        { route: 'reports.index', label: 'Reportes', icon: 'chart' },
    ];

    // Menu para AYUDANTE (sin usuarios ni reportes)
    const ayudanteMenu = [
        { route: 'dashboard', label: 'Dashboard', icon: 'home' },
        { route: 'orders.index', label: 'Pedidos', icon: 'clipboard' },
        { route: 'portfolio.index', label: 'Portafolio', icon: 'image' },
        { route: 'inventory.index', label: 'Inventario', icon: 'box' },
        { route: 'payments.index', label: 'Pagos', icon: 'credit-card' },
        { route: 'promotions.index', label: 'Promociones', icon: 'tag' },
        { route: 'reviews.index', label: 'Reseñas', icon: 'star' },
    ];

    // Menu para CLIENTE
    const clienteMenu = [
        { route: 'cliente.mi-cuenta', label: 'Mi Cuenta', icon: 'user' },
        { route: 'cliente.mis-pedidos', label: 'Mis Pedidos', icon: 'clipboard' },
        { route: 'public.catalogo', label: 'Ver Catálogo', icon: 'image' },
        { route: 'public.hacer-pedido', label: 'Hacer Pedido', icon: 'plus' },
    ];

    if (rol === 'PROPIETARIO') return fullMenu;
    if (rol === 'AYUDANTE') return ayudanteMenu;
    if (rol === 'CLIENTE') return clienteMenu;
    
    return [];
});

// Current Time
const currentTime = ref('');
const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleDateString('es-BO', { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    updateTime();
    setInterval(updateTime, 60000); // Actualizar cada minuto
});
</script>
