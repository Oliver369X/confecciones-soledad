<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    nombre_completo: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
});

const successMessage = ref('');
const errorMessage = ref('');

const submit = () => {
    console.log('🔵 [REGISTRO] Iniciando registro...', form.data());
    
    successMessage.value = '';
    errorMessage.value = '';
    
    form.post(route('register'), {
        onStart: () => {
            console.log('🔵 [REGISTRO] Enviando datos al servidor...');
        },
        onSuccess: (page) => {
            const msg = '✅ [REGISTRO] ¡Registro exitoso! Redirigiendo...';
            console.log(msg, page);
            successMessage.value = 'Registro exitoso. Redirigiendo a tu cuenta...';
        },
        onError: (errors) => {
            const msg = '❌ [REGISTRO] Error en el registro';
            console.error(msg, errors);
            errorMessage.value = 'Error en el registro. Revisa los campos e intenta de nuevo.';
            
            // Log detallado de cada error
            Object.keys(errors).forEach(field => {
                console.error(`  ❌ Campo "${field}": ${errors[field]}`);
            });
        },
        onFinish: () => {
            console.log('🔵 [REGISTRO] Petición completada');
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrarse" />

        <form @submit.prevent="submit">
            <!-- Mensaje de Éxito -->
            <div v-if="successMessage" class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                <p class="font-semibold">{{ successMessage }}</p>
            </div>

            <!-- Mensaje de Error General -->
            <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-semibold">{{ errorMessage }}</p>
            </div>

            <!-- Nombre Completo -->
            <div>
                <InputLabel for="nombre_completo" value="Nombre Completo" />

                <TextInput
                    id="nombre_completo"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.nombre_completo"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Ej: Juan Pérez"
                />

                <InputError class="mt-2" :message="form.errors.nombre_completo" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="email@ejemplo.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Teléfono -->
            <div class="mt-4">
                <InputLabel for="telefono" value="Teléfono (Opcional)" />

                <TextInput
                    id="telefono"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.telefono"
                    autocomplete="tel"
                    placeholder="75123456"
                />

                <InputError class="mt-2" :message="form.errors.telefono" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Contraseña"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    ¿Ya estás registrado?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Registrando...' : 'Registrarse' }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
