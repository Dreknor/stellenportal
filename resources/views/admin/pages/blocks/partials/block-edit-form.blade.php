{{-- Text Block --}}
@if($block->type === 'text')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Text-Inhalt') }}
        </label>
        <textarea name="content" rows="6" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ $block->content }}</textarea>
    </div>
@endif

{{-- Heading Block --}}
@if($block->type === 'heading')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Überschrift') }}
        </label>
        <input type="text" name="content" value="{{ $block->content }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Überschriften-Ebene') }}
        </label>
        <select name="settings[level]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="h1" {{ ($block->settings['level'] ?? 'h2') === 'h1' ? 'selected' : '' }}>H1 (Haupt-Überschrift)</option>
            <option value="h2" {{ ($block->settings['level'] ?? 'h2') === 'h2' ? 'selected' : '' }}>H2 (Unter-Überschrift)</option>
            <option value="h3" {{ ($block->settings['level'] ?? 'h2') === 'h3' ? 'selected' : '' }}>H3 (Kleine Überschrift)</option>
            <option value="h4" {{ ($block->settings['level'] ?? 'h2') === 'h4' ? 'selected' : '' }}>H4</option>
        </select>
    </div>
@endif

{{-- Image Block --}}
@if($block->type === 'image')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Bild auswählen') }}
        </label>
        @if($images->count() > 0)
            <div class="grid grid-cols-3 gap-2 mb-4">
                @foreach($images as $image)
                    <label class="cursor-pointer">
                        <input type="radio" name="settings[image_id]" value="{{ $image->id }}"
                               {{ ($block->settings['image_id'] ?? null) == $image->id ? 'checked' : '' }}
                               class="hidden peer">
                        <div class="border-2 border-gray-300 peer-checked:border-blue-500 rounded-lg overflow-hidden hover:border-blue-400 transition">
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full h-32 object-cover">
                        </div>
                    </label>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Keine Bilder vorhanden. Laden Sie zuerst Bilder hoch.') }}</p>
        @endif
        <a href="{{ route('cms.pages.images.index', $page) }}" class="text-blue-600 hover:text-blue-800 text-sm">
            {{ __('→ Bilder verwalten') }}
        </a>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Bild-Größe') }}
        </label>
        <select name="settings[size]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="small" {{ ($block->settings['size'] ?? 'medium') === 'small' ? 'selected' : '' }}>Klein</option>
            <option value="medium" {{ ($block->settings['size'] ?? 'medium') === 'medium' ? 'selected' : '' }}>Mittel</option>
            <option value="large" {{ ($block->settings['size'] ?? 'medium') === 'large' ? 'selected' : '' }}>Groß</option>
            <option value="full" {{ ($block->settings['size'] ?? 'medium') === 'full' ? 'selected' : '' }}>Volle Breite</option>
        </select>
    </div>
@endif

{{-- HTML Block --}}
@if($block->type === 'html')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('HTML-Code') }}
        </label>
        <textarea name="content" rows="8" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm">{{ $block->content }}</textarea>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Fügen Sie hier benutzerdefinierten HTML-Code ein.') }}</p>
    </div>
@endif

{{-- Quote Block --}}
@if($block->type === 'quote')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Zitat-Text') }}
        </label>
        <textarea name="content" rows="4" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ $block->content }}</textarea>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Autor') }}
        </label>
        <input type="text" name="settings[author]" value="{{ $block->settings['author'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="z.B. Max Mustermann">
    </div>
@endif

{{-- Button Block --}}
@if($block->type === 'button')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Button-Text') }} <span class="text-red-500">*</span>
        </label>
        <input type="text" name="content" value="{{ $block->content }}" required class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="z.B. Jetzt mehr erfahren">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Link-URL') }} <span class="text-red-500">*</span>
        </label>
        <input type="url" name="settings[url]" value="{{ $block->settings['url'] ?? '' }}" required class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="https://...">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Button-Stil') }}
        </label>
        <select name="settings[style]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="primary" {{ ($block->settings['style'] ?? 'primary') === 'primary' ? 'selected' : '' }}>Primär (Blau)</option>
            <option value="secondary" {{ ($block->settings['style'] ?? 'primary') === 'secondary' ? 'selected' : '' }}>Sekundär (Grau)</option>
            <option value="success" {{ ($block->settings['style'] ?? 'primary') === 'success' ? 'selected' : '' }}>Erfolg (Grün)</option>
            <option value="danger" {{ ($block->settings['style'] ?? 'primary') === 'danger' ? 'selected' : '' }}>Warnung (Rot)</option>
        </select>
    </div>
    <div class="mb-4">
        <div class="flex items-center">
            <input type="checkbox" name="settings[new_tab]" value="1" {{ ($block->settings['new_tab'] ?? false) ? 'checked' : '' }}
                   id="new_tab_{{ $block->id }}" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="new_tab_{{ $block->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('In neuem Tab öffnen') }}
            </label>
        </div>
    </div>
@endif

{{-- Divider Block --}}
@if($block->type === 'divider')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Trennlinien-Stil') }}
        </label>
        <select name="settings[style]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="solid" {{ ($block->settings['style'] ?? 'solid') === 'solid' ? 'selected' : '' }}>Durchgehend</option>
            <option value="dashed" {{ ($block->settings['style'] ?? 'solid') === 'dashed' ? 'selected' : '' }}>Gestrichelt</option>
            <option value="dotted" {{ ($block->settings['style'] ?? 'solid') === 'dotted' ? 'selected' : '' }}>Gepunktet</option>
            <option value="double" {{ ($block->settings['style'] ?? 'solid') === 'double' ? 'selected' : '' }}>Doppelt</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Abstand') }}
        </label>
        <select name="settings[spacing]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="small" {{ ($block->settings['spacing'] ?? 'medium') === 'small' ? 'selected' : '' }}>Klein</option>
            <option value="medium" {{ ($block->settings['spacing'] ?? 'medium') === 'medium' ? 'selected' : '' }}>Mittel</option>
            <option value="large" {{ ($block->settings['spacing'] ?? 'medium') === 'large' ? 'selected' : '' }}>Groß</option>
        </select>
    </div>
@endif

