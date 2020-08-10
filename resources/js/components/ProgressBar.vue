<template>
    <div class="relative w-full h-1 bg-gray-500 overflow-hidden">
        <div
            class="absolute inset-x-0 top-0 h-1 bg-green-500 z-0 transition-all duration-300 ease-in"
            :style="label"
        ></div>
    </div>
</template>

<script>
export default {
    name: 'ProgressBar',
    props: {
        progress: {
            type: Number,
            default: 0,
        },
    },
    computed: {
        label() {
            return `width: ${this.progress}%`;
        },
    },
};
</script>
