<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    notify_token: user.notify_token,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Line Notify Token</h2>
            <p class="mt-1 text-sm text-gray-600">Please enter your line notify token.</p>
            <a class="mt-1 text-xs text-gray-600 underline" href="/notify_intro" target="_blank"> How to use ?</a>
        </header>
        <form @submit.prevent="form.patch(route('profile.updateNotifyToken'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="notifyToken" value="Line Notify Token" />

                <TextInput
                    id="notifyToken"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.notify_token"
                    required
                    autofocus
                    autocomplete="notify_token"
                />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>

        </form>
    </section>
</template>