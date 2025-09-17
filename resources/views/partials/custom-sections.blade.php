@php
    $sections = \App\Models\CustomSection::where('page', $page)
        ->where('position', $position)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
@endphp

@if($sections->count() > 0)
    @foreach($sections as $section)
        <section class="custom-section" data-section-id="{{ $section->id }}">
            @if($section->title)
                <div class="skc-container">
                    <h2 class="skc-section-title">{{ $section->title }}</h2>
                    @if($section->subtitle)
                        <p class="skc-section-subtitle">{{ $section->subtitle }}</p>
                    @endif
                </div>
            @endif
            
            @if($section->content)
                <div class="skc-container">
                    <div class="custom-section-content">
                        {!! $section->content !!}
                    </div>
                </div>
            @endif
            
            @if($section->image)
                <div class="skc-container">
                    <div class="custom-section-image">
                        <img src="{{ Storage::url($section->image) }}" alt="{{ $section->title }}" class="img-fluid">
                    </div>
                </div>
            @endif
        </section>
    @endforeach
@endif
