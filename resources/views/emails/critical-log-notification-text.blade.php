{{ $log->level_name }} - {{ config('app.name') }}
================================================================

Zeitpunkt: {{ $log->created_at->format('d.m.Y H:i:s') }}
Level: {{ $log->level_name }}
Kanal: {{ $log->channel ?? 'N/A' }}

NACHRICHT:
----------------------------------------------------------------
{{ $log->message }}

@if($log->context && isset($log->context['exception']))
@php
    $exception = $log->context['exception'];
@endphp

EXCEPTION DETAILS:
----------------------------------------------------------------
@if(is_object($exception))
@if(method_exists($exception, 'getMessage'))
Message: {{ $exception->getMessage() }}
@endif
@if(method_exists($exception, 'getFile'))
File: {{ $exception->getFile() }}
@endif
@if(method_exists($exception, 'getLine'))
Line: {{ $exception->getLine() }}
@endif
@endif
@endif

@if($log->context && !empty($log->context))

CONTEXT:
----------------------------------------------------------------
{{ $log->formatted_context }}
@endif

================================================================
Details anzeigen: {{ route('admin.logs.show', $log->id) }}

Log-Eintrag ID: #{{ $log->id }}
{{ config('app.name') }}

