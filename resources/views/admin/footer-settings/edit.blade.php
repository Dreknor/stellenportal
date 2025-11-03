<x-layouts.app>
    @php
        $crumbs = [
            ['label' => __('Admin'), 'url' => route('admin.dashboard')],
            ['label' => __('Footer-Einstellungen'), 'url' => route('admin.footer-settings.index')],
            ['label' => __('Bearbeiten')],
        ];
    @endphp
    <x-breadcrumbs :breadcrumbs="$crumbs"/>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Footer-Einstellung bearbeiten') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.footer-settings.update', $footerSetting) }}" enctype="multipart/form-data" x-data="footerForm()">
            @csrf
            @method('PUT')

            <!-- Current Logo -->
            @if($footerSetting->logo_path)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Aktuelles Logo') }}
                    </label>
                    <img src="{{ $footerSetting->logo_url }}" alt="Current Logo" class="h-20 w-auto border border-gray-300 dark:border-gray-600 rounded">
                </div>
            @endif

            <!-- Logo -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Neues Logo hochladen') }}
                </label>
                <input type="file" name="logo" id="logo" accept="image/*"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       @change="previewLogo">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Erlaubte Formate: JPG, PNG, SVG. Maximale Größe: 2MB') }}</p>
                @error('logo')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div x-show="logoPreview" class="mt-4">
                    <img :src="logoPreview" alt="Logo Vorschau" class="h-20 w-auto border border-gray-300 dark:border-gray-600 rounded">
                </div>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Inhalt') }}
                </label>
                <textarea name="content" id="content" rows="5"
                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="{{ __('z.B. Copyright-Text, Adresse, etc.') }}">{{ old('content', $footerSetting->content) }}</textarea>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Sie können HTML verwenden.') }}</p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Links -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Links') }}
                </label>

                <template x-for="(link, index) in links" :key="index">
                    <div class="flex gap-4 mb-3">
                        <div class="flex-1">
                            <input type="text" :name="'links[' + index + '][title]'" x-model="link.title"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="{{ __('Link-Titel') }}">
                        </div>
                        <div class="flex-1">
                            <input type="url" :name="'links[' + index + '][url]'" x-model="link.url"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="{{ __('https://example.com') }}">
                        </div>
                        <div>
                            <x-button type="danger" size="sm" @click="removeLink(index)">
                                <x-fas-trash class="w-3"/>
                            </x-button>
                        </div>
                    </div>
                </template>

                <x-button type="secondary" @click="addLink">
                    <x-fas-plus class="w-3 mr-2"/>
                    {{ __('Link hinzufügen') }}
                </x-button>
            </div>

            <!-- Color Settings -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Farbeinstellungen') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Background Color -->
                    <div>
                        <label for="background_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Hintergrundfarbe') }}
                        </label>
                        <div class="flex gap-2">
                            <input type="color" id="background_color_picker" value="{{ old('background_color', $footerSetting->background_color ?? '#ffffff') }}"
                                   class="h-10 w-20 rounded border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   oninput="document.getElementById('background_color').value = this.value; document.getElementById('background_color_text').value = this.value;">
                            <input type="text" name="background_color" id="background_color" value="{{ old('background_color', $footerSetting->background_color ?? '#ffffff') }}" style="display:none;">
                            <input type="text" id="background_color_text" value="{{ old('background_color', $footerSetting->background_color ?? '#ffffff') }}"
                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="#ffffff"
                                   oninput="document.getElementById('background_color').value = this.value; document.getElementById('background_color_picker').value = this.value;">
                        </div>
                        @error('background_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Text Color -->
                    <div>
                        <label for="text_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Textfarbe') }}
                        </label>
                        <div class="flex gap-2">
                            <input type="color" id="text_color_picker" value="{{ old('text_color', $footerSetting->text_color ?? '#6b7280') }}"
                                   class="h-10 w-20 rounded border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   oninput="document.getElementById('text_color').value = this.value; document.getElementById('text_color_text').value = this.value;">
                            <input type="text" name="text_color" id="text_color" value="{{ old('text_color', $footerSetting->text_color ?? '#6b7280') }}" style="display:none;">
                            <input type="text" id="text_color_text" value="{{ old('text_color', $footerSetting->text_color ?? '#6b7280') }}"
                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="#6b7280"
                                   oninput="document.getElementById('text_color').value = this.value; document.getElementById('text_color_picker').value = this.value;">
                        </div>
                        @error('text_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Link Color -->
                    <div>
                        <label for="link_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Link-Farbe') }}
                        </label>
                        <div class="flex gap-2">
                            <input type="color" id="link_color_picker" value="{{ old('link_color', $footerSetting->link_color ?? '#2563eb') }}"
                                   class="h-10 w-20 rounded border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   oninput="document.getElementById('link_color').value = this.value; document.getElementById('link_color_text').value = this.value;">
                            <input type="text" name="link_color" id="link_color" value="{{ old('link_color', $footerSetting->link_color ?? '#2563eb') }}" style="display:none;">
                            <input type="text" id="link_color_text" value="{{ old('link_color', $footerSetting->link_color ?? '#2563eb') }}"
                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="#2563eb"
                                   oninput="document.getElementById('link_color').value = this.value; document.getElementById('link_color_picker').value = this.value;">
                        </div>
                        @error('link_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $footerSetting->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Als aktive Footer-Einstellung setzen') }}</span>
                </label>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Nur eine Footer-Einstellung kann gleichzeitig aktiv sein.') }}</p>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <x-button type="primary" native-type="submit">
                    <x-fas-save class="w-4 mr-2"/>
                    {{ __('Aktualisieren') }}
                </x-button>
                <x-button type="secondary" tag="a" :href="route('admin.footer-settings.index')">
                    {{ __('Abbrechen') }}
                </x-button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function footerForm() {
            return {
                logoPreview: null,
                links: @json(old('links', $footerSetting->links ?? [])),

                previewLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.logoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                addLink() {
                    this.links.push({ title: '', url: '' });
                },

                removeLink(index) {
                    this.links.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>

