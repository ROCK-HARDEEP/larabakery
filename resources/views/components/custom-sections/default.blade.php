<section class="custom-section" style="background-color: {{ $section->background_color ?? '#ffffff' }};">
    @if($section->image)
        <div class="w-full" style="height: 500px; overflow: hidden;">
            <img src="{{ Storage::url($section->image) }}" 
                 alt="{{ $section->title ?? '' }}" 
                 class="w-full h-full object-cover"
                 style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    @endif
    
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="text-center" style="color: {{ $section->text_color ?? '#000000' }};">
                @if($section->title)
                    <h2 class="text-3xl font-bold mb-4">{{ $section->title }}</h2>
                @endif
                
                @if($section->subtitle)
                    <h3 class="text-xl mb-6">{{ $section->subtitle }}</h3>
                @endif
                
                @if($section->content)
                    <div class="prose mx-auto mb-8">
                        {!! $section->content !!}
                    </div>
                @endif
                
                @if($section->button_text && $section->button_link)
                    <a href="{{ $section->button_link }}" 
                       class="btn {{ $section->button_style ?? 'btn-primary' }}">
                        {{ $section->button_text }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>