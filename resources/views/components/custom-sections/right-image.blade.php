<section class="custom-section" style="background-color: {{ $section->background_color ?? '#ffffff' }};">
    <div class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[500px]">
            <div class="flex items-center px-8 py-12 lg:px-16 order-2 lg:order-1">
                <div style="color: {{ $section->text_color ?? '#000000' }};">
                    @if($section->title)
                        <h2 class="text-3xl font-bold mb-4">{{ $section->title }}</h2>
                    @endif
                    
                    @if($section->subtitle)
                        <h3 class="text-xl mb-6">{{ $section->subtitle }}</h3>
                    @endif
                    
                    @if($section->content)
                        <div class="prose mb-8">
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
            
            @if($section->image)
                <div class="w-full h-full order-1 lg:order-2" style="min-height: 500px;">
                    <img src="{{ Storage::url($section->image) }}" 
                         alt="{{ $section->title ?? '' }}" 
                         class="w-full h-full object-cover"
                         style="width: 100%; height: 100%; min-height: 500px; object-fit: cover;">
                </div>
            @endif
        </div>
    </div>
</section>