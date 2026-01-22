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

{{-- Row Block --}}
@if($block->type === 'row')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Container-Breite') }}
        </label>
        <select name="settings[width]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="full" {{ ($block->settings['width'] ?? 'container') === 'full' ? 'selected' : '' }}>Volle Breite</option>
            <option value="container" {{ ($block->settings['width'] ?? 'container') === 'container' ? 'selected' : '' }}>Standard Container</option>
            <option value="narrow" {{ ($block->settings['width'] ?? 'container') === 'narrow' ? 'selected' : '' }}>Schmal</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Hintergrundfarbe') }}
        </label>
        <div class="flex gap-3">
            <input type="color"
                   name="background_color"
                   value="{{ $block->background_color ?? '#ffffff' }}"
                   class="w-20 h-12 rounded border-2 border-gray-300 dark:border-gray-600 cursor-pointer"
                   id="bg_color_{{ $block->id }}">
            <input type="text"
                   value="{{ $block->background_color ?? '#ffffff' }}"
                   class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 font-mono"
                   id="bg_color_text_{{ $block->id }}"
                   placeholder="#ffffff">
        </div>
        <div class="mt-2 flex gap-2 flex-wrap">
            <button type="button" onclick="setColor('{{ $block->id }}', 'transparent')" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded">Transparent</button>
            <button type="button" onclick="setColor('{{ $block->id }}', '#ffffff')" class="px-3 py-1 text-xs bg-white border hover:bg-gray-50 rounded">Weiß</button>
            <button type="button" onclick="setColor('{{ $block->id }}', '#f3f4f6')" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">Grau</button>
            <button type="button" onclick="setColor('{{ $block->id }}', '#dbeafe')" class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 rounded">Blau</button>
            <button type="button" onclick="setColor('{{ $block->id }}', '#1e40af')" class="px-3 py-1 text-xs bg-blue-700 text-white hover:bg-blue-800 rounded">Dunkelblau</button>
        </div>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Innenabstand (Padding)') }}
        </label>
        <select name="settings[padding]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="none" {{ ($block->settings['padding'] ?? 'medium') === 'none' ? 'selected' : '' }}>Kein</option>
            <option value="small" {{ ($block->settings['padding'] ?? 'medium') === 'small' ? 'selected' : '' }}>Klein</option>
            <option value="medium" {{ ($block->settings['padding'] ?? 'medium') === 'medium' ? 'selected' : '' }}>Mittel</option>
            <option value="large" {{ ($block->settings['padding'] ?? 'medium') === 'large' ? 'selected' : '' }}>Groß</option>
        </select>
    </div>
    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-200">
                <p class="font-semibold mb-1">{{ __('Verschachtelte Blöcke') }}</p>
                <p>{{ __('Blöcke können per Drag & Drop in diesen Container verschoben werden. Neue Blöcke können direkt im Container erstellt werden.') }}</p>
                <p class="mt-2 text-xs">{{ __('Das Content-Feld wird nicht mehr für Container verwendet.') }}</p>
            </div>
        </div>
    </div>
@endif

{{-- Columns Block --}}
@if($block->type === 'columns')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Anzahl der Spalten (Desktop)') }}
        </label>
        <select name="settings[columns]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="1" {{ ($block->settings['columns'] ?? '2') === '1' ? 'selected' : '' }}>1 Spalte</option>
            <option value="2" {{ ($block->settings['columns'] ?? '2') === '2' ? 'selected' : '' }}>2 Spalten</option>
            <option value="3" {{ ($block->settings['columns'] ?? '2') === '3' ? 'selected' : '' }}>3 Spalten</option>
            <option value="4" {{ ($block->settings['columns'] ?? '2') === '4' ? 'selected' : '' }}>4 Spalten</option>
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Spaltenabstand') }}
        </label>
        <select name="settings[gap]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="small" {{ ($block->settings['gap'] ?? 'medium') === 'small' ? 'selected' : '' }}>Klein</option>
            <option value="medium" {{ ($block->settings['gap'] ?? 'medium') === 'medium' ? 'selected' : '' }}>Mittel</option>
            <option value="large" {{ ($block->settings['gap'] ?? 'medium') === 'large' ? 'selected' : '' }}>Groß</option>
        </select>
    </div>
    <div class="mb-4">
        <div class="flex items-center mb-2">
            <input type="checkbox" name="settings[equal_height]" value="1" {{ ($block->settings['equal_height'] ?? false) ? 'checked' : '' }}
                   id="equal_height_{{ $block->id }}" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="equal_height_{{ $block->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Gleiche Höhe für alle Spalten') }}
            </label>
        </div>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Spalten-Inhalt (HTML)') }}
        </label>
        <textarea name="content" rows="10" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm" placeholder="<div>Spalte 1</div>&#10;<div>Spalte 2</div>">{!! $block->content !!}</textarea>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Jedes <div>...</div> repräsentiert eine Spalte. Fügen Sie HTML-Inhalt hinzu.') }}</p>
    </div>
@endif

{{-- Card Block --}}
@if($block->type === 'card')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Card-Titel') }}
        </label>
        <input type="text" name="settings[title]" value="{{ $block->settings['title'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="Titel der Card">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Card-Text') }}
        </label>
        <textarea name="content" rows="5" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ $block->content }}</textarea>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Icon-Klasse (optional)') }}
        </label>
        <input type="text" name="settings[icon]" value="{{ $block->settings['icon'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="z.B. fa-check-circle">
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('FontAwesome Icon-Klasse') }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Button-Text (optional)') }}
        </label>
        <input type="text" name="settings[button_text]" value="{{ $block->settings['button_text'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="z.B. Mehr erfahren">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Button-URL (optional)') }}
        </label>
        <input type="url" name="settings[button_url]" value="{{ $block->settings['button_url'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="https://...">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Card-Stil') }}
        </label>
        <select name="settings[style]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="default" {{ ($block->settings['style'] ?? 'default') === 'default' ? 'selected' : '' }}>Standard</option>
            <option value="bordered" {{ ($block->settings['style'] ?? 'default') === 'bordered' ? 'selected' : '' }}>Mit Rahmen</option>
            <option value="shadow" {{ ($block->settings['style'] ?? 'default') === 'shadow' ? 'selected' : '' }}>Mit Schatten</option>
            <option value="elevated" {{ ($block->settings['style'] ?? 'default') === 'elevated' ? 'selected' : '' }}>Erhöht</option>
        </select>
    </div>
@endif

{{-- Card with Image Block --}}
@if($block->type === 'card_image')
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Header-Bild auswählen') }}
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
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Keine Bilder vorhanden.') }}</p>
        @endif
        <a href="{{ route('cms.pages.images.index', $page) }}" class="text-blue-600 hover:text-blue-800 text-sm">
            {{ __('→ Bilder verwalten') }}
        </a>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Card-Titel') }}
        </label>
        <input type="text" name="settings[title]" value="{{ $block->settings['title'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="Titel der Card">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Card-Text') }}
        </label>
        <textarea name="content" rows="5" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ $block->content }}</textarea>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Button-Text (optional)') }}
        </label>
        <input type="text" name="settings[button_text]" value="{{ $block->settings['button_text'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="z.B. Mehr erfahren">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Button-URL (optional)') }}
        </label>
        <input type="url" name="settings[button_url]" value="{{ $block->settings['button_url'] ?? '' }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" placeholder="https://...">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Bild-Position') }}
        </label>
        <select name="settings[image_position]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="top" {{ ($block->settings['image_position'] ?? 'top') === 'top' ? 'selected' : '' }}>Oben</option>
            <option value="left" {{ ($block->settings['image_position'] ?? 'top') === 'left' ? 'selected' : '' }}>Links</option>
            <option value="right" {{ ($block->settings['image_position'] ?? 'top') === 'right' ? 'selected' : '' }}>Rechts</option>
        </select>
    </div>
@endif

{{-- Allgemeine Block-Einstellungen (für alle Block-Typen) --}}
<div class="border-t-2 border-gray-200 dark:border-gray-600 pt-6 mt-6">
    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        {{ __('Block-Einstellungen') }}
    </h3>

    {{-- Max Width --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Block-Breite') }}
        </label>
        <select name="settings[block_max_width]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="" {{ !isset($block->settings['block_max_width']) ? 'selected' : '' }}>Standard (100%)</option>
            <option value="sm" {{ ($block->settings['block_max_width'] ?? '') === 'sm' ? 'selected' : '' }}>Klein (max-w-3xl)</option>
            <option value="md" {{ ($block->settings['block_max_width'] ?? '') === 'md' ? 'selected' : '' }}>Mittel (max-w-5xl)</option>
            <option value="lg" {{ ($block->settings['block_max_width'] ?? '') === 'lg' ? 'selected' : '' }}>Groß (max-w-7xl)</option>
            <option value="xl" {{ ($block->settings['block_max_width'] ?? '') === 'xl' ? 'selected' : '' }}>Extra Groß (max-w-screen-2xl)</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Definiert die maximale Breite dieses Blocks') }}</p>
    </div>

    {{-- Background Color --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Hintergrundfarbe') }}
        </label>
        <div class="flex gap-3 items-start">
            <div class="flex-1">
                <input type="color" name="settings[block_background_color]" id="block_bg_color_{{ $block->id }}"
                       value="{{ $block->settings['block_background_color'] ?? '#ffffff' }}"
                       class="w-full h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 cursor-pointer">
            </div>
            <div class="flex-1">
                <input type="text" id="block_bg_color_text_{{ $block->id }}"
                       value="{{ $block->settings['block_background_color'] ?? 'transparent' }}"
                       placeholder="#ffffff"
                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm">
            </div>
            <button type="button" onclick="setBlockColor({{ $block->id }}, 'transparent')"
                    class="px-4 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 rounded-lg transition-all whitespace-nowrap">
                {{ __('Transparent') }}
            </button>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Die Hintergrundfarbe dieses Blocks') }}</p>
    </div>

    {{-- Margin/Spacing --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Außenabstand (Margin)') }}
        </label>
        <select name="settings[block_margin]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="" {{ !isset($block->settings['block_margin']) ? 'selected' : '' }}>Standard</option>
            <option value="none" {{ ($block->settings['block_margin'] ?? '') === 'none' ? 'selected' : '' }}>Kein Abstand</option>
            <option value="sm" {{ ($block->settings['block_margin'] ?? '') === 'sm' ? 'selected' : '' }}>Klein</option>
            <option value="md" {{ ($block->settings['block_margin'] ?? '') === 'md' ? 'selected' : '' }}>Mittel</option>
            <option value="lg" {{ ($block->settings['block_margin'] ?? '') === 'lg' ? 'selected' : '' }}>Groß</option>
            <option value="xl" {{ ($block->settings['block_margin'] ?? '') === 'xl' ? 'selected' : '' }}>Extra Groß</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Abstand zum vorherigen/nächsten Block') }}</p>
    </div>

    {{-- Padding --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Innenabstand (Padding)') }}
        </label>
        <select name="settings[block_padding]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="" {{ !isset($block->settings['block_padding']) ? 'selected' : '' }}>Standard</option>
            <option value="none" {{ ($block->settings['block_padding'] ?? '') === 'none' ? 'selected' : '' }}>Kein Abstand</option>
            <option value="sm" {{ ($block->settings['block_padding'] ?? '') === 'sm' ? 'selected' : '' }}>Klein</option>
            <option value="md" {{ ($block->settings['block_padding'] ?? '') === 'md' ? 'selected' : '' }}>Mittel</option>
            <option value="lg" {{ ($block->settings['block_padding'] ?? '') === 'lg' ? 'selected' : '' }}>Groß</option>
            <option value="xl" {{ ($block->settings['block_padding'] ?? '') === 'xl' ? 'selected' : '' }}>Extra Groß</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Abstand innerhalb des Blocks') }}</p>
    </div>

    {{-- Border Radius --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Ecken-Rundung') }}
        </label>
        <select name="settings[block_rounded]" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            <option value="" {{ !isset($block->settings['block_rounded']) ? 'selected' : '' }}>Standard</option>
            <option value="none" {{ ($block->settings['block_rounded'] ?? '') === 'none' ? 'selected' : '' }}>Keine Rundung</option>
            <option value="sm" {{ ($block->settings['block_rounded'] ?? '') === 'sm' ? 'selected' : '' }}>Klein</option>
            <option value="md" {{ ($block->settings['block_rounded'] ?? '') === 'md' ? 'selected' : '' }}>Mittel</option>
            <option value="lg" {{ ($block->settings['block_rounded'] ?? '') === 'lg' ? 'selected' : '' }}>Groß</option>
            <option value="full" {{ ($block->settings['block_rounded'] ?? '') === 'full' ? 'selected' : '' }}>Vollständig rund</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Abrundung der Block-Ecken') }}</p>
    </div>

    {{-- Custom CSS Class --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Eigene CSS-Klasse') }}
        </label>
        <input type="text" name="settings[block_css_class]" value="{{ $block->settings['block_css_class'] ?? '' }}"
               class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 font-mono text-sm"
               placeholder="my-custom-class">
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Fügen Sie eine eigene CSS-Klasse hinzu (ohne Punkt)') }}</p>
    </div>

    {{-- Custom CSS --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('Individuelles CSS für diesen Block') }}
        </label>
        <textarea name="settings[block_custom_css]" rows="6"
                  class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 font-mono text-sm"
                  placeholder="/* CSS nur für diesen Block */&#10;.my-class {&#10;    color: #333;&#10;}">{{ $block->settings['block_custom_css'] ?? '' }}</textarea>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('CSS wird nur auf diesen Block angewendet') }}</p>
    </div>
</div>

<script>
// Color picker sync for block settings
function setBlockColor(blockId, color) {
    const colorInput = document.getElementById('block_bg_color_' + blockId);
    const textInput = document.getElementById('block_bg_color_text_' + blockId);

    if (color === 'transparent') {
        colorInput.value = '#ffffff';
        textInput.value = 'transparent';
    } else {
        colorInput.value = color;
        textInput.value = color;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const blockId = {{ $block->id }};
    const colorInput = document.getElementById('block_bg_color_' + blockId);
    const textInput = document.getElementById('block_bg_color_text_' + blockId);

    if (colorInput && textInput) {
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
        });

        textInput.addEventListener('input', function() {
            if (this.value !== 'transparent' && this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                colorInput.value = this.value;
            }
        });
    }
});
</script>
