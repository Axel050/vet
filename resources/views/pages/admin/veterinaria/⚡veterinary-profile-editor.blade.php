<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\VeterinaryProfile as ProfileModel;
use App\Models\VeterinarySocialLink;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Forms\VeterinaryProfile;

new #[Title('Editor de Perfil')] class extends Component {
    use WithFileUploads;

    public VeterinaryProfile $form;

    // Social Links
    public $socialLinks = [];
    public $newPlatform = 'facebook';
    public $newUrl = '';

    public function mount()
    {
        $veterinary = auth()->user()->veterinary;

        if ($veterinary->plan !== 'pro' && $veterinary->plan !== 'free') {
            abort(403, 'Esta función solo está disponible para el Plan PRO.');
        }

        $profile = $veterinary->profile ?? ProfileModel::create(['veterinary_id' => $veterinary->id]);

        $this->form->setProfile($profile);

        $this->loadSocialLinks();
    }

    public function loadSocialLinks()
    {
        $this->socialLinks = auth()->user()->veterinary->socialLinks()->get()->toArray();
    }

    public function save()
    {
        $this->form->update();

        $this->dispatch('notify', message: 'Perfil actualizado correctamente', type: 'success');
    }

    public function addSocialLink()
    {
        $this->validate(
            [
                'newUrl' => 'required|url',
                'newPlatform' => 'required|string',
            ],
            [
                'newUrl.required' => 'La URL es obligatoria',
                'newUrl.url' => 'La URL debe ser válida',
                'newPlatform.required' => 'La plataforma es obligatoria',
                'newPlatform.string' => 'La plataforma debe ser texto',
            ],
        );

        auth()
            ->user()
            ->veterinary->socialLinks()
            ->create([
                'platform' => $this->newPlatform,
                'url' => $this->newUrl,
            ]);

        $this->newUrl = '';
        $this->loadSocialLinks();
    }

    public function deleteSocialLink($id)
    {
        $link = auth()->user()->veterinary->socialLinks()->where('id', $id)->firstOrFail();

        $link->delete();

        $this->loadSocialLinks();
    }

    public function removeCoverImage()
    {
        $this->form->removeCoverImage();
        $this->dispatch('notify', message: 'Imagen de portada eliminada', type: 'success');
    }

    public function removeLogo()
    {
        $this->form->removeLogo();
        $this->dispatch('notify', message: 'Logo eliminado', type: 'success');
    }
};

?>

<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 md:py-10 py-4">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-9 text-white sm:text-3xl sm:truncate">
                Configuración de Página Pública
            </h2>
            <p class="mt-1 text-sm text-gray-400">
                Personaliza cómo se ve tu veterinaria ante el mundo. Esta página es pública para tus clientes.
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('public.veterinary', auth()->user()->veterinary->slug) }}" target="_blank"
                class="inline-flex items-center px-4 py-2 border border-gray-700 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Ver Página Pública
            </a>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-7 gap-8">
        <!-- Left Column: Basic Info & Hero -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Hero Section -->
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden">
                <div class="md:p-6 p-3 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white">Sección Principal (Hero)</h3>
                </div>
                <div class="md:p-6 p-3 space-y-6">
                    <div class="grid grid-cols-1 md:gap-6 gap-4">
                        <x-input label="Título del Hero" model="form.hero_title"
                            placeholder="Ej: Expertos en salud y bienestar animal" />
                        <x-textarea label="Subtítulo o Eslogan" model="form.hero_subtitle"
                            placeholder="Ej: Cuidamos a tus mascotas como si fueran nuestras. Tecnología y amor para cada paciente."
                            rows="2" />
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-300">Imagen de Portada (Hero
                            Background)</label>
                        <div class="flex md:flex-row flex-col items-center md:gap-6 gap-4">
                            @if ($form->cover_image)
                                <img src="{{ $form->cover_image->temporaryUrl() }}"
                                    class="md:w-40 w-35 md:h-24 h-20 object-cover rounded-lg border border-gray-700">
                            @elseif($form->profile?->cover_image)
                                <img src="{{ asset('storage/' . $form->profile->cover_image) }}"
                                    class="md:w-40 w-35 md:h-24 h-20 object-cover rounded-lg border border-gray-700">
                            @else
                                <div
                                    class="md:w-40 w-35 md:h-24 h-20 bg-gray-900 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-700">
                                    <svg class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex flex-col gap-2">
                                <input type="file" wire:model="form.cover_image"
                                    class="text-xs text-gray-400 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600 cursor-pointer">

                                @if ($form->cover_image || $form->profile?->cover_image)
                                    <button type="button" wire:click="removeCoverImage"
                                        class="text-xs text-red-500 hover:text-red-400 font-medium text-left w-fit">
                                        Eliminar imagen
                                    </button>
                                @endif
                            </div>

                        </div>
                        @error('form.cover_image')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden ">

                <div class="md:p-6 p-3 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white">Sobre Nosotros</h3>
                </div>

                <div class="flex md:flex-row flex-col ">
                    <div class="md:p-6 p-3">
                        <x-input label="Años en el mercado" model="form.years_in_business" type="number" min="0"
                            placeholder="Ej: 5" />
                    </div>
                    <div class="md:p-6 p-3 flex-1">
                        <x-textarea label="Descripción Detallada" model="form.description"
                            placeholder="Cuéntale a tus clientes por qué deberían elegirte..." rows="6" />
                    </div>
                </div>
            </div>

            <!-- Save Button -->

        </div>

        <!-- Right Column: Contact & Social -->
        <div class="lg:col-span-3 space-y-8">
            <!-- Contact Info -->
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden">
                <div class="md:p-6 p-3 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white">Información de Contacto</h3>
                </div>
                <div class="md:p-6 p-3 md:space-y-4 space-y-3">
                    <div class="flex items-center gap-4">
                        @if ($form->logo)
                            <img src="{{ $form->logo->temporaryUrl() }}"
                                class="size-16 object-cover rounded-xl border border-gray-700">
                        @elseif($form->profile?->logo)
                            <img src="{{ asset('storage/' . $form->profile->logo) }}"
                                class="size-16 object-cover rounded-xl border border-gray-700">
                        @else
                            <div
                                class="size-16 bg-gray-900 rounded-xl flex items-center justify-center border-2 border-dashed border-gray-700">
                                <span class="text-[10px] text-gray-600 uppercase font-bold">Logo</span>
                            </div>
                        @endif
                        <div class="flex flex-col gap-2">
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Logo
                                    de la Veterinaria</label>
                                <input type="file" wire:model="form.logo"
                                    class="text-xs text-gray-400 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600 cursor-pointer">
                            </div>

                            @if ($form->logo || $form->profile?->logo)
                                <button type="button" wire:click="removeLogo"
                                    class="text-xs text-red-500 hover:text-red-400 font-medium text-left w-fit mt-1">
                                    Eliminar logo
                                </button>
                            @endif
                        </div>
                    </div>
                    @error('form.logo')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror

                    <x-input label="Dirección Local" model="form.address" icon="map-pin" />
                    <x-input label="Teléfono Fijo" model="form.phone" icon="phone" />
                    <x-input label="WhatsApp (Solo números)" model="form.whatsapp" icon="whatsapp"
                        placeholder="Ej: 5491122334455" />
                </div>
            </div>

            <!-- Social Links -->
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden">
                <div class="md:p-6 p-3 border-b border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-white">Redes Sociales</h3>
                </div>
                <div class="md:p-6 p-3 md:space-y-4 space-y-3">
                    <div class="flex md:flex-row flex-wrap flex-col gap-2 pr-1">
                        <select wire:model="newPlatform"
                            class="bg-gray-900 border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="youtube">YouTube</option>
                        </select>

                        <div class="flex gap-2 ">
                            <input type="text" wire:model="newUrl" placeholder="https://..."
                                class="flex-1 bg-gray-900 border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5 ">
                            <button wire:click="addSocialLink"
                                class="p-2.5 bg-indigo-600 rounded-lg text-white hover:bg-indigo-700">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('newUrl')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror

                    <div class="space-y-2 mt-4">
                        @foreach ($socialLinks as $link)
                            <div
                                class="flex items-center justify-between bg-gray-900 p-3 rounded-xl border border-gray-700">
                                <div class="flex  flex-wrap items-center gap-3">
                                    <span
                                        class="text-xs font-bold uppercase text-gray-500">{{ $link['platform'] }}</span>
                                    <span
                                        class="text-xs text-gray-400 truncate max-w-[150px]">{{ $link['url'] }}</span>
                                </div>
                                <button wire:click="deleteSocialLink({{ $link['id'] }})"
                                    class="text-red-500 hover:text-red-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center md:col-span-3">
            <button wire:click="save"
                class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-indigo-500/25 order-7">
                Guardar Cambios
            </button>
        </div>

    </div>
</div>
