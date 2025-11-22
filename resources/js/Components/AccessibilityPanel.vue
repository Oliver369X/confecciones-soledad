<template>
    <!-- Botón Flotante -->
    <button @click="showPanel = !showPanel"
            class="fixed bottom-6 right-6 bg-purple-600 text-white p-4 rounded-full shadow-2xl hover:bg-purple-700 transition transform hover:scale-110 z-40">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
    </button>

    <!-- Panel de Accesibilidad -->
    <transition name="slide-up">
        <div v-if="showPanel"
             class="fixed bottom-24 right-6 bg-white rounded-2xl shadow-2xl border-2 border-purple-200 p-6 w-80 z-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-gray-900">♿ Accesibilidad</h3>
                <button @click="showPanel = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Tamaño de Texto -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    📏 Tamaño de texto
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <button v-for="size in textSizes" :key="size.id"
                            @click="cambiarTamano(size.id)"
                            :class="[
                                'p-3 rounded-lg border-2 transition font-semibold',
                                tamanoActual === size.id
                                    ? 'border-purple-600 bg-purple-50 text-purple-700'
                                    : 'border-gray-200 hover:border-purple-300 text-gray-700'
                            ]">
                        {{ size.label }}
                    </button>
                </div>
            </div>

            <!-- Alto Contraste -->
            <div class="mb-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">
                        🎨 Alto Contraste
                    </span>
                    <div class="relative">
                        <input type="checkbox" 
                               v-model="altoContraste"
                               @change="toggleContraste"
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                    </div>
                </label>
            </div>

            <!-- Restablecer -->
            <button @click="restablecer"
                    class="w-full mt-4 py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium">
                🔄 Restablecer
            </button>
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showPanel = ref(false);
const tamanoActual = ref('normal');
const altoContraste = ref(false);

const textSizes = [
    { id: 'normal', label: 'A' },
    { id: 'grande', label: 'A+' },
    { id: 'muy-grande', label: 'A++' },
];

const cambiarTamano = (tamano) => {
    tamanoActual.value = tamano;
    document.documentElement.setAttribute('data-text-size', tamano);
    localStorage.setItem('tamano-texto', tamano);
};

const toggleContraste = () => {
    document.documentElement.setAttribute('data-high-contrast', altoContraste.value);
    localStorage.setItem('alto-contraste', altoContraste.value);
};

const restablecer = () => {
    cambiarTamano('normal');
    altoContraste.value = false;
    document.documentElement.setAttribute('data-high-contrast', 'false');
    localStorage.removeItem('tamano-texto');
    localStorage.removeItem('alto-contraste');
};

onMounted(() => {
    // Cargar preferencias guardadas
    const tamanoGuardado = localStorage.getItem('tamano-texto') || 'normal';
    cambiarTamano(tamanoGuardado);

    const contrasteGuardado = localStorage.getItem('alto-contraste') === 'true';
    altoContraste.value = contrasteGuardado;
    document.documentElement.setAttribute('data-high-contrast', contrasteGuardado);
});
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
    transform: translateY(20px);
    opacity: 0;
}
</style>
