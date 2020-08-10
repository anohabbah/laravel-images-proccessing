@extends('app')

@section('body')
    <div class="bg-white w-full mx-auto shadow sm:rounded-sm p-4">
        <file-uploader
            upload-action="{{ route('product.store_image', $product->id) }}"
            remove-action="{{ route('product.destroy_image', $product->id) }}"
            accept="image/*"
            :multiple="true"
            :assets="{{ $assets }}"
            v-slot="{ processing, trigger, files, hasFiles, errors, dragging, listeners, fileProgress, remove }"
        >
            <div>
                <div class="md:flex md:items-center md:justify-between pb-3 mb-3 border-b border-solid border-gray-300">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-normal leading-7 text-gray-600 sm:leading-9 sm:truncate">
                            {{ $product->name }}
                        </h1>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-sm text-white bg-gray-600 hover:bg-gray-500 focus:outline-none focus:shadow-outline-gray focus:border-gray-700 active:bg-gray-700 transition duration-150 ease-in-out"
                            :class="{ 'opacity-50': processing }"
                            @click="trigger"
                        >
                            <span v-if="!processing">Select image</span>
                            <span v-else>Processing</span>
                        </button>
                    </div>
                </div>
                <div class="relative w-full">
                    <div v-if="processing" class="absolute inset-x-0 top-0">
                        <progress-bar :progress="fileProgress"></progress-bar>
                    </div>
                    <div>
                        <div v-if="hasFiles">
                            <div class="grid grid-cols-4 gap-2 mb-2">
                                <div v-for="file in files" :key="file.id">
                                    <img :src="file.url" :alt="file.name" class="mb-2">
                                    <div class="grid grid-cols-4 gap-2 mb-2">
                                        <div class="bg-gray-300">
                                            <a :href="file.variants.sm.url" target="_blank">
                                                <span class="block text-center py-2">sm</span>
                                                <img :src="file.variants.sm.url" :alt="file.name">
                                            </a>
                                        </div>
                                        <div class="bg-gray-300">
                                            <a :href="file.variants.md.url" target="_blank">
                                                <span class="block text-center py-2">md</span>
                                                <img :src="file.variants.md.url" :alt="file.name">
                                            </a>
                                        </div>
                                        <div class="bg-gray-300">
                                            <a :href="file.variants.lg.url" target="_blank">
                                                <span class="block text-center py-2">lg</span>
                                                <img :src="file.variants.lg.url" :alt="file.name">
                                            </a>
                                        </div>
                                        <div class="bg-gray-300">
                                            <a :href="file.variants.xl.url" target="_blank">
                                                <span class="block text-center py-2">xl</span>
                                                <img :src="file.variants.xl.url" :alt="file.name">
                                            </a>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="block w-full items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-sm text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:shadow-outline-red focus:border-red-700 active:bg-red-700 transition duration-150 ease-in-out"
                                        @click="remove(file.id)"
                                    >
                                        Remove image
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="!hasFiles && !processing"
                            :class="{ 'bg-gray-200': dragging }"
                            class="flex items-center justify-center h-32 mb-2 text-gray-500 border border-dashed border-gray-300 cursor-pointer"
                            v-on="listeners"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="fill-current h-10">
                                <path
                                    d="M0 6c0-1.1.9-2 2-2h3l2-2h6l2 2h3a2 2 0 012 2v10a2 2 0 01-2 2H2a2 2 0 01-2-2V6zm10 10a5 5 0 100-10 5 5 0 000 10zm0-2a3 3 0 110-6 3 3 0 010 6z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </file-uploader>
    </div>
@endsection
