<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    item: Object,
});

const form = useForm({
    titulo: props.item.titulo,
    descripcion: props.item.descripcion,
    imagen_url_principal: props.item.imagen_url_principal,
    imagen_url_antes: props.item.imagen_url_antes,
    imagen_url_despues: props.item.imagen_url_despues,
    publicado: Boolean(props.item.publicado),
});

const submit = () => {
    form.put(route('portfolio.update', props.item.portafolio_id));
};
</script>

<template>
    <Head title="Editar Item de Portafolio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editar Item de Portafolio</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="titulo" value="Título" />
                                <TextInput
                                    id="titulo"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.titulo"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.titulo" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="descripcion" value="Descripción" />
                                <textarea
                                    id="descripcion"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.descripcion"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.descripcion" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="imagen_url_principal" value="URL Imagen Principal" />
                                <TextInput
                                    id="imagen_url_principal"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.imagen_url_principal"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.imagen_url_principal" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="imagen_url_antes" value="URL Imagen Antes (Opcional)" />
                                <TextInput
                                    id="imagen_url_antes"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.imagen_url_antes"
                                />
                                <InputError class="mt-2" :message="form.errors.imagen_url_antes" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="imagen_url_despues" value="URL Imagen Después (Opcional)" />
                                <TextInput
                                    id="imagen_url_despues"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.imagen_url_despues"
                                />
                                <InputError class="mt-2" :message="form.errors.imagen_url_despues" />
                            </div>

                            <div class="block mt-4">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.publicado" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Publicado</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('portfolio.index')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                    Cancelar
                                </Link>
                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Actualizar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
