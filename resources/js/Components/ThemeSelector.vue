<template>
    <div class="relative">
        <!-- Botón Selector de Tema -->
        <button @click="showPanel = !showPanel"
                class="flex items-center space-x-2 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <span class="hidden md:inline">Tema</span>
        </button>

        <!-- Panel de Selección -->
        <div v-if="showPanel"
             class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border p-4 z-50">
            <h3 class="font-bold text-gray-900 mb-3">Elige tu tema</h3>
            
            <div class="space-y-2">
                <button v-for="tema in temas" :key="tema.id"
                        @click="cambiarTema(tema.id)"
                        :class="[
                            'w-full p-3 rounded-lg border-2 transition text-left',
                            temaActual === tema.id
                                ? 'border-purple-600 bg-purple-50'
                                : 'border-gray-200 hover:border-purple-300'
                        ]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold">{{ tema.nombre }}</p>
                            <p class="text-xs text-gray-500">{{ tema.descripcion }}</p>
                        </div>
                        <span v-if="temaActual === tema.id" class="text-purple-600">✓</span>
                    </div>
                </button>
            </div>

            <div class="mt-4 pt-4 border-t">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="text-sm font-medium">Modo Noche</span>
                    <input type="checkbox" 
                           v-model="modoNocheManual"
                           @change="toggleModoNoche"
                           class="w-10 h-5 rounded-full">
                </label>
                <p class="text-xs text-gray-500 mt-1">
                    {{ modoAuto ? '🌓 Automático según hora' : '🕹️ Manual' }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const showPanel = ref(false);
const temaActual = ref('jovenes');
const modoNocheManual = ref(false);
const modoAuto = ref(true);

const temas = [
    { id: 'ninos', nombre: '🎨 Niños', descripcion: 'Colorido y divertido' },
    { id: 'jovenes', nombre: '🚀 Jóvenes', descripcion: 'Moderno y vibrante' },
    { id: 'adultos', nombre: '💼 Adultos', descripcion: 'Profesional y elegante' },
];

const cambiarTema = (tema) => {
    temaActual.value = tema;
    document.documentElement.setAttribute('data-theme', tema);
    localStorage.setItem('tema-preferido', tema);
};

const toggleModoNoche = () => {
    modoAuto.value = false;
    const modo = modoNocheManual.value ? 'noche' : 'dia';
    document.documentElement.setAttribute('data-mode', modo);
    localStorage.setItem('modo-noche-manual', modoNocheManual.value);
    localStorage.setItem('modo-auto', 'false');
};

const aplicarModoAutomatico = () => {
    const hora = new Date().getHours();
    const esNoche = hora >= 18 || hora < 6; // 6pm - 6am
    document.documentElement.setAttribute('data-mode', esNoche ? 'noche' : 'dia');
    modoNocheManual.value = esNoche;
};

onMounted(() => {
    // Cargar tema guardado
    const temaGuardado = localStorage.getItem('tema-preferido') || 'jovenes';
    cambiarTema(temaGuardado);

    // Verificar si el modo es automático o manual
    const autoMode = localStorage.getItem('modo-auto') !== 'false';
    modoAuto.value = autoMode;

    if (autoMode) {
        aplicarModoAutomatico();
        // Actualizar cada hora
        setInterval(aplicarModoAutomatico, 3600000);
    } else {
        const modoManual = localStorage.getItem('modo-noche-manual') === 'true';
        modoNocheManual.value = modoManual;
        document.documentElement.setAttribute('data-mode', modoManual ? 'noche' : 'dia');
    }
});
</script>
