<!-- Attachments -->
<div class="mt-6 border-t border-gray-700 pt-6">
    <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wider">Archivos Adjuntos</h3>

    @can('pro-veterinaria')
        <div class="space-y-4 md:flex md:gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300">Subir Archivos (Imágenes o PDF)</label>
                <input type="file" wire:model="form.attachments" multiple accept=".pdf,.jpg,.jpeg,.png,.webp"
                    class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition-all cursor-pointer">
                <p class="mt-1 text-xs text-gray-500">Puedes seleccionar hasta 2 archivos a la vez. Peso máximo 10MB
                    por archivo.</p>
                <div wire:loading wire:target="form.attachments">
                    <div class="flex items-center gap-2">
                        <div class="animate-spin rounded-full size-4 border-b-2 border-indigo-600"></div>
                        <span class="text-xs text-gray-400">Subiendo archivos...</span>
                    </div>
                </div>
                @error('form.attachments')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('form.attachments.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Previews for new uploads -->
            @if ($form->attachments && count(array_filter($form->attachments)) > 0)
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-400 mb-2">Archivos listos para subir:</h4>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($form->attachments as $index => $attachment)
                            @if ($attachment)
                                <div class="flex flex-col items-center gap-2">
                                    <div
                                        class="relative group size-24 bg-gray-800 rounded-lg border border-gray-600 flex flex-col items-center justify-center p-2 ">
                                        @if (in_array(strtolower($attachment->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp']))
                                            <img src="{{ $attachment->temporaryUrl() }}"
                                                class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity">
                                        @else
                                            <svg class="size-8 text-red-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <span
                                                class="text-[10px] text-white bg-black bg-opacity-50 px-1 rounded truncate mt-1 break-all w-full text-center relative z-10">{{ Str::limit($attachment->getClientOriginalName(), 15) }}</span>
                                        @endif

                                        <button type="button" wire:click="removeAttachment({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 z-20">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <label class="flex items-center space-x-2 text-xs text-gray-400 cursor-pointer">
                                        <input type="checkbox" wire:model="form.attachments_visibility.{{ $index }}"
                                            class="rounded bg-gray-900 border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span>Público</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="bg-gray-800/50 border border-emerald-500/30 rounded-xl md:p-6 p-3 text-center shadow-sm">

            <div
                class="mx-auto flex items-center justify-center rounded-full bg-emerald-500/20 mb-3 md:p-2 p-1.5 md:px-4 gap-1 md:gap-4 w-fit">
                <svg class="md:size-6 size-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <h4 class="mx:text-lg text-base font-semibold text-gray-200 ">Exclusivo para plan PRO</h4>
            </div>
            <p class="text-gray-400 text-sm mb-4 max-w-md mx-auto">
                La subida de archivos adjuntos (radiografías, análisis, fotos, etc.) está reservada
                exclusivamente para veterinarias con suscripción PRO.
            </p>
            <a href=""
                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 md:py-2 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition duration-200">
                Mejorar a PRO
            </a>
        </div>
    @endcan

    <!-- Existing files -->
    @if ($form->record && count($form->existing_attachments) > 0)
        <div>
            <h4 class="text-sm font-medium text-gray-400 mb-2">Archivos guardados:</h4>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($form->existing_attachments as $file)
                    <div
                        class="group relative bg-gray-800 rounded-xl border border-gray-700 flex flex-col items-center justify-center p-3 h-32 hover:border-indigo-500 transition-colors">
                        @if ($file->type === 'image')
                            <div class="absolute inset-0 rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $file->file_path) }}"
                                    class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-black/40 md:opacity-0 opacity-75 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                        class="p-1.5 bg-gray-900/80 rounded-lg text-white hover:text-indigo-400"
                                        title="Ver archivo">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <button type="button" wire:click="toggleAttachmentVisibility({{ $file->id }})"
                                        class="p-1.5 {{ $file->is_visible_to_owner ? 'bg-green-600/80 hover:bg-green-700' : 'bg-gray-600/80 hover:bg-gray-700' }} rounded-lg text-white"
                                        title="{{ $file->is_visible_to_owner ? 'Hacer Privado' : 'Hacer Público' }}">
                                        @if ($file->is_visible_to_owner)
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        @endif
                                    </button>
                                    <button type="button" wire:confirm="¿Estás seguro de eliminar este archivo?"
                                        wire:click="deleteAttachment({{ $file->id }})"
                                        class="p-1.5 bg-red-600/80 rounded-lg text-white hover:bg-red-700"
                                        title="Eliminar archivo">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <svg class="h-10 w-10 text-red-500 mb-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs text-gray-300 font-medium truncate w-full px-2 text-center"
                                title="{{ $file->original_name }}">{{ $file->original_name }}</span>

                            <div
                                class="absolute inset-0 bg-gray-900/80 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                    class="p-1.5 bg-gray-800 rounded-lg text-white hover:text-indigo-400"
                                    title="Ver archivo">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button type="button" wire:click="toggleAttachmentVisibility({{ $file->id }})"
                                    class="p-1.5 {{ $file->is_visible_to_owner ? 'bg-green-600/80 hover:bg-green-700' : 'bg-gray-600/80 hover:bg-gray-700' }} rounded-lg text-white"
                                    title="{{ $file->is_visible_to_owner ? 'Hacer Privado' : 'Hacer Público' }}">
                                    @if ($file->is_visible_to_owner)
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    @endif
                                </button>
                                <button type="button" wire:confirm="¿Estás seguro de eliminar este archivo?"
                                    wire:click="deleteAttachment({{ $file->id }})"
                                    class="p-1.5 bg-red-600 rounded-lg text-white hover:bg-red-700"
                                    title="Eliminar archivo">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
