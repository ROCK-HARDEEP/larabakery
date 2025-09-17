<section class="custom-section hero-section relative" 
         style="min-height: 600px; background-color: {{ $section->background_color ?? '#ffffff' }};">
    @if($section->image)
        <div class="absolute inset-0 w-full h-full">
            <img src="{{ Storage::url($section->image) }}" 
                 alt="{{ $section->title ?? '' }}"
                 class="w-full h-full object-cover"
                 style="width: 100%; height: 100%; object-fit: cover;">
            <div class="absolute inset-0 bg-black opacity-50"></div>
        </div>
    @endif
    
    <div class="container mx-auto px-4 relative z-10 py-24">
        <div class="max-w-4xl mx-auto text-center" style="color: {{ $section->image ? '#ffffff' : ($section->text_color ?? '#000000') }};">
            @if($section->title)
                <h1 class="text-5xl font-bold mb-4">{{ $section->title }}</h1>
            @endif
            
            @if($section->subtitle)
                <h2 class="text-2xl mb-8">{{ $section->subtitle }}</h2>
            @endif
            
            @if($section->content)
                <div class="prose prose-lg mx-auto mb-8 {{ $section->image ? 'text-white' : '' }}">
                    {!! $section->content !!}
                </div>
            @endif
            
            @if($section->button_text && $section->button_link)
                <a href="{{ $section->button_link }}" 
                   class="btn btn-lg {{ $section->button_style ?? 'btn-primary' }}">
                    {{ $section->button_text }}
                </a>
            @endif
        </div>
    </div>
</section>