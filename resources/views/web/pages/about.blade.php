@extends('web.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="skc-hero-section" style="height: 400px;">
        <div class="skc-hero-slider">
            <div class="skc-hero-slide active">
                @if($aboutUs->story_image)
                    <img src="{{ Storage::url($aboutUs->story_image) }}" alt="About Us" class="skc-hero-image">
                @else
                    <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1600" alt="About Us" class="skc-hero-image">
                @endif
                <div class="skc-hero-content">
                    <h1 class="skc-hero-title">{{ $aboutUs->story_title }}</h1>
                    <p class="skc-hero-subtitle">{{ $aboutUs->story_content }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="skc-section">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">{{ $aboutUs->began_title }}</h2>
                <p class="skc-section-subtitle">{{ $aboutUs->began_quote }}</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-top: 40px;">
                <div>
                    @php
                        $paragraphs = explode("\n", $aboutUs->began_content);
                    @endphp
                    @foreach($paragraphs as $paragraph)
                        @if(trim($paragraph))
                            <p style="font-size: 18px; line-height: 1.8; color: var(--skc-medium-gray); margin-bottom: 20px;">
                                {{ $paragraph }}
                            </p>
                        @endif
                    @endforeach
                </div>
                
                <div style="position: relative;">
                    <div style="background: linear-gradient(135deg, var(--skc-orange), #e88c0c); border-radius: 20px; padding: 40px; text-align: center; color: white;">
                        <i class="fas fa-award" style="font-size: 60px; margin-bottom: 20px;"></i>
                        <h3 style="font-size: 28px; font-weight: 700; margin-bottom: 15px;">Award Winning</h3>
                        <p style="font-size: 16px; opacity: 0.9;">
                            Recognized for excellence in baking and customer service
                        </p>
                    </div>
                    
                    <!-- Floating Stats -->
                    <div style="position: absolute; top: -30px; left: -30px; background: white; border-radius: 15px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <div style="text-align: center;">
                            <div style="font-size: 36px; font-weight: 700; color: var(--skc-orange);">{{ $aboutUs->years_experience }}</div>
                            <div style="font-size: 14px; color: var(--skc-medium-gray);">Years Experience</div>
                        </div>
                    </div>
                    
                    <div style="position: absolute; bottom: -30px; right: -30px; background: white; border-radius: 15px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <div style="text-align: center;">
                            <div style="font-size: 36px; font-weight: 700; color: var(--skc-orange);">{{ $aboutUs->happy_customers }}</div>
                            <div style="font-size: 14px; color: var(--skc-medium-gray);">Happy Customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    @if($aboutUs->values && count($aboutUs->values) > 0)
    <section class="skc-section" style="background: var(--skc-light-gray);">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Our Values</h2>
                <p class="skc-section-subtitle">The principles that guide everything we do</p>
            </div>

            @if(count($aboutUs->values) <= 4)
                <!-- Static grid for 4 or fewer values -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; max-width: 1200px; margin: 0 auto;">
                    @foreach($aboutUs->values as $value)
                    <div style="text-align: center; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                        <div style="width: 80px; height: 80px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                            <i class="{{ $value['icon'] ?? 'fas fa-star' }}" style="font-size: 36px; color: var(--skc-orange);"></i>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px;">{{ $value['title'] ?? '' }}</h3>
                        <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                            {{ $value['description'] ?? '' }}
                        </p>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Auto-scrolling carousel for more than 4 values -->
                <div class="swiper values-swiper">
                    <div class="swiper-wrapper">
                        @foreach($aboutUs->values as $value)
                        <div class="swiper-slide">
                            <div style="text-align: center; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); height: 100%;">
                                <div style="width: 80px; height: 80px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                                    <i class="{{ $value['icon'] ?? 'fas fa-star' }}" style="font-size: 36px; color: var(--skc-orange);"></i>
                                </div>
                                <h3 style="font-size: 24px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px;">{{ $value['title'] ?? '' }}</h3>
                                <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                                    {{ $value['description'] ?? '' }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Team Section -->
    @if($aboutUs->team_members && count($aboutUs->team_members) > 0)
    <section class="skc-section">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Meet Our Team</h2>
                <p class="skc-section-subtitle">The passionate people behind every delicious creation</p>
            </div>

            @if(count($aboutUs->team_members) <= 4)
                <!-- Static grid for 4 or fewer team members -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; max-width: 1200px; margin: 0 auto;">
                    @foreach($aboutUs->team_members as $member)
                    <div style="text-align: center; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                        <div style="width: 120px; height: 120px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; overflow: hidden;">
                            @if(isset($member['image']) && $member['image'])
                                <img src="{{ Storage::url($member['image']) }}" alt="{{ $member['name'] ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-user" style="font-size: 48px; color: var(--skc-orange);"></i>
                            @endif
                        </div>
                        <h3 style="font-size: 22px; font-weight: 600; color: var(--skc-black); margin-bottom: 10px;">{{ $member['name'] ?? '' }}</h3>
                        <p style="color: var(--skc-orange); font-weight: 500; margin-bottom: 15px;">{{ $member['designation'] ?? '' }}</p>
                        <p style="color: var(--skc-medium-gray); line-height: 1.6; font-size: 14px;">
                            {{ $member['description'] ?? '' }}
                        </p>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Auto-scrolling carousel for more than 4 team members -->
                <div class="swiper team-swiper">
                    <div class="swiper-wrapper">
                        @foreach($aboutUs->team_members as $member)
                        <div class="swiper-slide">
                            <div style="text-align: center; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); height: 100%;">
                                <div style="width: 120px; height: 120px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; overflow: hidden;">
                                    @if(isset($member['image']) && $member['image'])
                                        <img src="{{ Storage::url($member['image']) }}" alt="{{ $member['name'] ?? '' }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="fas fa-user" style="font-size: 48px; color: var(--skc-orange);"></i>
                                    @endif
                                </div>
                                <h3 style="font-size: 22px; font-weight: 600; color: var(--skc-black); margin-bottom: 10px;">{{ $member['name'] ?? '' }}</h3>
                                <p style="color: var(--skc-orange); font-weight: 500; margin-bottom: 15px;">{{ $member['designation'] ?? '' }}</p>
                                <p style="color: var(--skc-medium-gray); line-height: 1.6; font-size: 14px;">
                                    {{ $member['description'] ?? '' }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="skc-section" style="
        @if(isset($aboutUs->cta_background_image) && $aboutUs->cta_background_image)
            background-image: url('{{ Storage::url($aboutUs->cta_background_image) }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        @else
            background: {{ $aboutUs->cta_section_color ? 'linear-gradient(135deg, ' . $aboutUs->cta_section_color . ', ' . adjustBrightness($aboutUs->cta_section_color, 20) . ')' : 'linear-gradient(135deg, var(--skc-black), #333)' }};
        @endif
    ">
        @if(isset($aboutUs->cta_background_image) && $aboutUs->cta_background_image)
            <!-- Dark overlay for better text readability when using image -->
            <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5);"></div>
        @endif
        <div class="skc-container" style="position: relative; z-index: 1;">
            <div style="text-align: center; color: white;">
                <h2 style="font-size: 36px; font-weight: 700; margin-bottom: 20px;">{{ $aboutUs->cta_title }}</h2>
                <p style="font-size: 18px; margin-bottom: 30px; opacity: 0.9;">
                    {{ $aboutUs->cta_subtitle }}
                </p>
                @if($aboutUs->cta_button_text)
                    <a href="{{ $aboutUs->cta_button_link }}" class="skc-hero-btn" style="background: {{ $aboutUs->cta_button_color }}; color: white;">
                        {{ $aboutUs->cta_button_text }}
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Values Carousel - Only initialize if more than 4 values
    if (document.querySelector('.values-swiper')) {
        new Swiper('.values-swiper', {
            slidesPerView: 4,
            spaceBetween: 25,
            loop: true,
            loopedSlides: 8,
            centeredSlides: false,
            allowTouchMove: false,
            simulateTouch: false,
            grabCursor: false,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                pauseOnMouseEnter: false,  // Disabled hover pause
                stopOnLastSlide: false,
                waitForTransition: false
            },
            speed: 8000,  // Faster speed (was 18000)
            freeMode: {
                enabled: false
            },
            resistance: false,
            resistanceRatio: 0,
            breakpoints: {
                320: {
                    slidesPerView: 1.2,
                    spaceBetween: 12,
                    speed: 10000  // Faster speed
                },
                768: {
                    slidesPerView: 2.5,
                    spaceBetween: 18,
                    speed: 9000  // Faster speed
                },
                1024: {
                    slidesPerView: 4,  // Show 4 cards
                    spaceBetween: 25,
                    speed: 8000  // Faster speed
                }
            }
        });
    }

    // Team Carousel - Only initialize if more than 4 team members
    if (document.querySelector('.team-swiper')) {
        new Swiper('.team-swiper', {
            slidesPerView: 4,
            spaceBetween: 25,
            loop: true,
            loopedSlides: 8,
            centeredSlides: false,
            allowTouchMove: false,
            simulateTouch: false,
            grabCursor: false,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                pauseOnMouseEnter: false,  // Disabled hover pause
                reverseDirection: true,
                stopOnLastSlide: false,
                waitForTransition: false
            },
            speed: 9000,  // Faster speed (was 20000)
            freeMode: {
                enabled: false
            },
            resistance: false,
            resistanceRatio: 0,
            breakpoints: {
                320: {
                    slidesPerView: 1.2,
                    spaceBetween: 12,
                    speed: 11000  // Faster speed
                },
                768: {
                    slidesPerView: 2.5,
                    spaceBetween: 18,
                    speed: 10000  // Faster speed
                },
                1024: {
                    slidesPerView: 4,  // Show 4 cards
                    spaceBetween: 25,
                    speed: 9000  // Faster speed
                }
            }
        });
    }

    // Add CSS for smooth rendering and disable hover
    const style = document.createElement('style');
    style.textContent = `
        /* Smooth linear animation */
        .values-swiper .swiper-wrapper,
        .team-swiper .swiper-wrapper {
            transition-timing-function: linear !important;
            -webkit-transition-timing-function: linear !important;
        }

        /* Disable ALL interactions and hover effects */
        .values-swiper,
        .team-swiper,
        .values-swiper *,
        .team-swiper * {
            pointer-events: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
        }

        /* Remove any hover effects */
        .values-swiper .swiper-slide:hover,
        .team-swiper .swiper-slide:hover,
        .values-swiper .swiper-slide > div:hover,
        .team-swiper .swiper-slide > div:hover {
            transform: none !important;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08) !important;
        }

        /* Hardware acceleration for smooth scrolling */
        .values-swiper .swiper-slide,
        .team-swiper .swiper-slide {
            will-change: transform;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-font-smoothing: antialiased;
        }

        /* Hide pagination dots */
        .swiper-pagination {
            display: none !important;
        }

        /* Ensure smooth infinite scroll */
        .values-swiper .swiper-wrapper,
        .team-swiper .swiper-wrapper {
            -webkit-transform-style: preserve-3d;
            transform-style: preserve-3d;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .values-swiper .swiper-wrapper,
            .team-swiper .swiper-wrapper {
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
            }
        }

        /* Prevent cursor changes */
        .values-swiper,
        .team-swiper,
        .values-swiper .swiper-container,
        .team-swiper .swiper-container,
        .values-swiper .swiper-wrapper,
        .team-swiper .swiper-wrapper,
        .values-swiper .swiper-slide,
        .team-swiper .swiper-slide,
        .values-swiper .swiper-slide *,
        .team-swiper .swiper-slide * {
            cursor: default !important;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush

@php
function adjustBrightness($hex, $percent) {
    // Remove the hash if present
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Calculate new values
    $r = min(255, max(0, $r + ($r * $percent / 100)));
    $g = min(255, max(0, $g + ($g * $percent / 100)));
    $b = min(255, max(0, $b + ($b * $percent / 100)));
    
    // Convert back to hex
    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}
@endphp