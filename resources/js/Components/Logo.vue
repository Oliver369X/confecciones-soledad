<template>
    <svg :width="size" :height="size" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Fondo círculo con gradiente -->
        <defs>
            <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#9333EA;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#2563EB;stop-opacity:1" />
            </linearGradient>
        </defs>
        
        <!-- Círculo de fondo -->
        <circle cx="50" cy="50" r="48" fill="url(#logoGradient)" />
        
        <!-- Tijeras (simplificadas) -->
        <path d="M30 25 L40 35 L30 45 L25 40 Z" fill="white" opacity="0.9"/>
        <path d="M30 55 L40 65 L30 75 L25 70 Z" fill="white" opacity="0.9"/>
        <path d="M40 35 L70 50 L40 65" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.9"/>
        
        <!-- Aguja e hilo -->
        <circle cx="70" cy="30" r="3" fill="white"/>
        <path d="M70 30 Q75 40 70 50" stroke="white" stroke-width="2" stroke-dasharray="2,2" opacity="0.8"/>
        
        <!-- Detalle decorativo -->
        <circle cx="50" cy="50" r="2" fill="white" opacity="0.6"/>
    </svg>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
    size: {
        type: [String, Number],
        default: '40'
    }
});
</script>
