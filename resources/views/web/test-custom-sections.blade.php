@extends('layouts.app')

@section('title', 'Test Custom Sections')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Test Custom Sections</h1>
        
        @if(isset($customSections['home']) && $customSections['home']->count() > 0)
            <h2 class="text-2xl font-semibold mb-4">Home Page Sections</h2>
            @foreach($customSections['home'] as $section)
                <x-custom-section :section="$section" />
            @endforeach
        @endif
        
        @if(isset($customSections['about']) && $customSections['about']->count() > 0)
            <h2 class="text-2xl font-semibold mb-4">About Page Sections</h2>
            @foreach($customSections['about'] as $section)
                <x-custom-section :section="$section" />
            @endforeach
        @endif
        
        @if(isset($customSections['contact']) && $customSections['contact']->count() > 0)
            <h2 class="text-2xl font-semibold mb-4">Contact Page Sections</h2>
            @foreach($customSections['contact'] as $section)
                <x-custom-section :section="$section" />
            @endforeach
        @endif
    </div>
@endsection