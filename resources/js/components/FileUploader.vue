<template>
    <div>
        <slot
            :processing="processing"
            :trigger="trigger"
            :remove="remove"
            :files="files"
            :hasFiles="hasFiles"
            :errors="errors"
            :dragging="dragging"
            :listeners="listeners"
            :fileProgress="fileProgress"
            :totalProgress="totalProgress"
        ></slot>
        <input
            type="file"
            :accept="accept"
            @change="change"
            :multiple="multiple"
            ref="upload"
            class="hidden"
        />
    </div>
</template>
<script>
import ApiCaller from '../core/ApiCaller';
export default {
    name: 'FileUploader',
    props: {
        accept: {
            type: String,
            default: '*',
        },
        multiple: {
            type: Boolean,
            default: true,
        },
        uploadAction: {
            type: String,
            required: true,
        },
        removeAction: {
            type: String,
            required: true,
        },
        field: {
            type: String,
            default: 'file',
        },
        assets: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            processing: false,
            fileProgress: 0,
            totalProgress: 0,
            numberOfFiles: 0,
            files: this.assets,
            errors: [],
            dragging: false,
            listeners: {
                ...this.$listeners,
                ...{
                    dragenter: event => {
                        this.stopEvent(event);
                        this.dragging = true;
                    },
                    dragover: event => {
                        this.stopEvent(event);
                        this.dragging = true;
                    },
                    dragleave: event => {
                        this.stopEvent(event);
                        this.dragging = false;
                    },
                    drop: event => {
                        this.stopEvent(event);
                        this.dragging = false;
                        if (this.processing) {
                            return;
                        }
                        this.select(event.dataTransfer.files);
                    },
                    click: event => {
                        this.stopEvent(event);
                        if (this.processing) {
                            return;
                        }
                        this.trigger();
                    },
                },
            },
        };
    },
    computed: {
        input() {
            return this.$refs.upload;
        },
        hasFiles() {
            return this.files.length > 0;
        },
    },
    methods: {
        stopEvent(event) {
            event.stopPropagation();
            event.preventDefault();
        },
        trigger() {
            this.input.click();
        },
        reset() {
            this.errors = [];
            this.fileProgress = 0;
            this.totalProgress = 0;
            this.numberOfFiles = 0;
        },
        clear() {
            this.input.value = null;
            this.input.type = 'text';
            this.input.type = 'file';
        },
        change() {
            this.select(this.input.files);
        },
        select(selected) {
            if (this.processing) {
                return;
            }
            this.reset();
            const files = Array.from(selected);
            if ((this.numberOfFiles = files.length) === 0) {
                return;
            }
            this.clear();
            this.send(files);
        },
        send(files) {
            this.processing = true;
            this.makeCall(files, 0);
        },
        preload(src) {
            return new Promise((resolve, reject) => {
                const image = new Image();
                image.onload = resolve;
                image.onerror = reject;
                image.src = src;
            });
        },
        process(response, callback) {
            if (response.preload.length > 0) {
                let promises = [];
                for (const url of response.preload) {
                    promises.push(this.preload(url));
                }
                Promise.all(promises)
                    .then(callback)
                    .catch(error => {
                        this.errors.push(
                            error.message || response.name + ' preload failed'
                        );
                        callback();
                    });
            } else {
                callback();
            }
        },
        makeCall(files, index) {
            this.upload(files[index])
                .then(response => {
                    this.process(response.data, () => {
                        if (this.multiple) {
                            this.files = [...this.files, ...[response.data]];
                        } else {
                            this.files = [response.data];
                        }
                        this.reCall(files, index);
                    });
                })
                .catch(error => {
                    this.errors.push(
                        error.message || files[index] + ' upload failed'
                    );
                    this.reCall(files, index);
                });
        },
        reCall(files, index) {
            let nextIndex = index + 1;
            if (nextIndex < this.numberOfFiles) {
                this.updateTotalProgress();
                this.makeCall(files, nextIndex);
            } else {
                this.processing = false;
            }
        },
        updateTotalProgress() {
            this.totalProgress = Math.round(
                (this.files.length * 100) / this.numberOfFiles
            );
        },
        upload(file) {
            let data = new FormData();
            data.append(this.field, file);

            return ApiCaller.request(this.uploadAction, 'post', data, {
                onUploadProgress: this.updateFileProgress,
            });
        },
        updateFileProgress(event) {
            this.fileProgress = Math.round((event.loaded * 100) / event.total);
        },
        remove(id) {
            ApiCaller.request(this.removeAction, 'delete', { file: id }).then(
                () => {
                    this.files = this.files.filter(asset => asset.id !== id);
                }
            );
        },
    },
};
</script>
