@props([
    'name',
    'id' => null,
    'required' => false,
    'accept' => '*/*',
    'placeholder' => 'Drag your documents, photos, or videos here to start uploading.',
    'existingUrl' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div 
    x-data="{
        isDragOver: false,
        fileName: '',
        fileSize: '',
        previewUrl: '{{ $existingUrl }}',
        isImage: false,
        
        init() {
            this.syncPreview();
        },
        
        syncPreview() {
            if (this.previewUrl) {
                const cleanUrl = this.previewUrl.split('?')[0];
                const ext = cleanUrl.split('.').pop().toLowerCase();
                this.isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].includes(ext);
            } else {
                this.isImage = false;
            }
        },
        
        handleFileChange(e) {
            const files = e.target.files || e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                this.fileName = file.name;
                this.fileSize = this.formatBytes(file.size);
                
                this.isImage = file.type.startsWith('image/');
                
                // Show preview if image
                if (this.isImage) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.previewUrl = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.previewUrl = '';
                }
            }
        },
        
        handleDrop(e) {
            this.isDragOver = false;
            if (e.dataTransfer.files.length > 0) {
                this.$refs.fileInput.files = e.dataTransfer.files;
                // Dispatch change event manually so handleFileChange catches it
                const event = new Event('change', { bubbles: true });
                this.$refs.fileInput.dispatchEvent(event);
            }
        },
        
        clearFile() {
            this.fileName = '';
            this.fileSize = '';
            this.previewUrl = '';
            this.isImage = false;
            this.$refs.fileInput.value = '';
            // Dispatch change event
            const event = new Event('change', { bubbles: true });
            this.$refs.fileInput.dispatchEvent(event);
        },
        
        formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    }"
    x-on:report-opened.window="previewUrl = $event.detail.existingUrl || ''; fileName = ''; fileSize = ''; if($refs.fileInput) $refs.fileInput.value = ''; syncPreview();"
    class="relative font-sans"
>
    <!-- Drag and Drop Dropzone -->
    <div 
        @dragover.prevent="isDragOver = true"
        @dragleave.prevent="isDragOver = false"
        @drop.prevent="handleDrop"
        :class="isDragOver ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900'"
        class="flex flex-col items-center justify-center border-2 border-dashed rounded-3xl p-6 md:p-8 text-center transition-all duration-200"
    >
        <!-- Native Hidden File Input -->
        <input 
            x-ref="fileInput"
            type="file" 
            name="{{ $name }}" 
            id="{{ $inputId }}"
            accept="{{ $accept }}"
            @change="handleFileChange"
            :required="typeof editMode !== 'undefined' ? !editMode : {{ $required ? 'true' : 'false' }}"
            class="sr-only"
        >

        <!-- Dynamic Content Area -->
        <template x-if="!fileName && !previewUrl">
            <div class="flex flex-col items-center space-y-3">
                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-black text-[9px] uppercase tracking-widest rounded-full font-mono">
                    SELECT FILE
                </span>
                
                <!-- Helper Text -->
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 max-w-xs leading-relaxed">
                    {{ $placeholder }}
                </p>

                <!-- Browse Button -->
                <button 
                    type="button" 
                    @click="$refs.fileInput.click()"
                    class="inline-flex items-center min-h-10 px-5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-sm active:scale-95 transition-all select-none cursor-pointer"
                >
                    Browse files
                </button>
            </div>
        </template>

        <!-- Selected File State -->
        <template x-if="fileName || previewUrl">
            <div class="w-full flex flex-col items-center">
                <!-- Preview Container -->
                <div class="relative group mb-4">
                    <template x-if="isImage && previewUrl">
                        <div class="relative w-28 h-28 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-sm bg-slate-50 dark:bg-slate-950 animate-fade-in">
                            <img :src="previewUrl" class="w-full h-full object-cover" alt="File preview">
                        </div>
                    </template>
                    <template x-if="!isImage || !previewUrl">
                        <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-inner animate-fade-in">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </template>
                </div>

                <!-- File Info -->
                <div class="max-w-md">
                    <span class="block text-xs font-bold text-slate-850 dark:text-white truncate max-w-[250px] mx-auto text-center" x-text="fileName || 'Active File'"></span>
                    <span class="block text-[10px] text-slate-400 font-semibold mt-0.5 text-center" x-text="fileSize || 'Currently Active'"></span>
                </div>

                <!-- Actions Row -->
                <div class="flex items-center gap-3 mt-4">
                    <button 
                        type="button" 
                        @click="$refs.fileInput.click()"
                        class="px-4 py-2 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-850 font-bold rounded-xl text-[10px] uppercase tracking-wider transition active:scale-95"
                    >
                        Change File
                    </button>
                    <button 
                        type="button" 
                        @click="clearFile"
                        class="px-4 py-2 bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/40 font-bold rounded-xl text-[10px] uppercase tracking-wider transition active:scale-95"
                    >
                        Remove
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
