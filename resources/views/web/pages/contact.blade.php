@extends('web.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="skc-hero-section" style="height: 400px;">
        <div class="skc-hero-slider">
            <div class="skc-hero-slide active">
                @if(isset($contactUs->get_in_touch_image) && $contactUs->get_in_touch_image)
                    <img src="{{ Storage::url($contactUs->get_in_touch_image) }}" alt="{{ $contactUs->get_in_touch_title }}" class="skc-hero-image">
                @else
                    <img src="https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=1600" alt="Contact Us" class="skc-hero-image">
                @endif
                <div class="skc-hero-content">
                    <h1 class="skc-hero-title">{{ $contactUs->get_in_touch_title ?? 'Get in Touch' }}</h1>
                    <p class="skc-hero-subtitle">{{ $contactUs->get_in_touch_quote ?? 'Have a question, special request, or just want to say hello? We\'d love to hear from you!' }}</p>
                    @if(isset($contactUs->get_in_touch_button_text) && $contactUs->get_in_touch_button_text)
                        <a href="{{ $contactUs->get_in_touch_button_link ?? '/contact' }}" class="skc-hero-btn" style="margin-top: 20px; display: inline-block; background: var(--skc-orange); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                            {{ $contactUs->get_in_touch_button_text }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="skc-section">
        <div class="skc-container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
                <!-- Contact Form -->
                <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <h2 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Send us a Message</h2>
                    
                    <form class="space-y-6">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label for="first_name" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required
                                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            </div>
                            <div>
                                <label for="last_name" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required
                                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                            </div>
                        </div>
                        
                        <div>
                            <label for="email" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Email *</label>
                            <input type="email" id="email" name="email" required
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                        </div>
                        
                        <div>
                            <label for="phone" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Phone</label>
                            <input type="tel" id="phone" name="phone"
                                   style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s;">
                        </div>
                        
                        <div>
                            <label for="subject" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Subject *</label>
                            <select id="subject" name="subject" required
                                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s; background: white;">
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="order">Order Question</option>
                                <option value="custom">Custom Cake Request</option>
                                <option value="feedback">Feedback</option>
                                <option value="partnership">Partnership</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="message" style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Message *</label>
                            <textarea id="message" name="message" rows="5" required
                                      style="width: 100%; padding: 12px 16px; border: 1px solid var(--skc-border); border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.2s; resize: vertical;"
                                      placeholder="Tell us what's on your mind..."></textarea>
                        </div>
                        
                        <button type="submit" 
                                style="width: 100%; background: var(--skc-orange); color: white; padding: 16px 24px; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 5px 15px rgba(246, 157, 28, 0.3);">
                            <i class="fas fa-paper-plane" style="margin-right: 10px;"></i>Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div style="space-y: 8;">
                    <!-- Contact Details -->
                    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 30px;">
                        <h2 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Contact Information</h2>
                        
                        <div style="space-y: 6;">
                            @if($contactUs->contact_address)
                            <div style="display: flex; align-items: start; gap: 20px; margin-bottom: 25px;">
                                <div style="width: 50px; height: 50px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-map-marker-alt" style="font-size: 20px; color: var(--skc-orange);"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Address</h3>
                                    <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                                        {!! nl2br(e($contactUs->contact_address)) !!}
                                    </p>
                                </div>
                            </div>
                            @endif
                            
                            @if($contactUs->contact_phone)
                            <div style="display: flex; align-items: start; gap: 20px; margin-bottom: 25px;">
                                <div style="width: 50px; height: 50px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-phone" style="font-size: 20px; color: var(--skc-orange);"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Phone</h3>
                                    <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                                        {{ $contactUs->contact_phone }}
                                    </p>
                                </div>
                            </div>
                            @endif
                            
                            @if($contactUs->contact_email)
                            <div style="display: flex; align-items: start; gap: 20px; margin-bottom: 25px;">
                                <div style="width: 50px; height: 50px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-envelope" style="font-size: 20px; color: var(--skc-orange);"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Email</h3>
                                    <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                                        <a href="mailto:{{ $contactUs->contact_email }}" style="color: var(--skc-medium-gray); text-decoration: none;">
                                            {{ $contactUs->contact_email }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                            @endif
                            
                            @if($contactUs->business_hours && count($contactUs->business_hours) > 0)
                            <div style="display: flex; align-items: start; gap: 20px;">
                                <div style="width: 50px; height: 50px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-clock" style="font-size: 20px; color: var(--skc-orange);"></i>
                                </div>
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">Business Hours</h3>
                                    <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                                        @foreach($contactUs->business_hours as $hours)
                                            {{ $hours['day'] }}: {{ $hours['hours'] }}<br>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Social Media -->
                    @if($contactUs->social_media_links && count($contactUs->social_media_links) > 0)
                    <div style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <h2 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin-bottom: 30px;">Follow Us</h2>
                        <p style="color: var(--skc-medium-gray); margin-bottom: 25px; line-height: 1.6;">
                            Stay connected with us on social media for the latest updates, special offers, and behind-the-scenes content.
                        </p>
                        
                        <div style="display: flex; gap: 15px;">
                            @foreach($contactUs->social_media_links as $social)
                                @if(!empty($social['url']))
                                <a href="{{ $social['url'] }}" 
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   style="width: 50px; height: 50px; background: var(--skc-orange); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.2s;"
                                   onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 5px 15px rgba(246, 157, 28, 0.4)';"
                                   onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                    <i class="{{ $social['icon'] ?? 'fas fa-link' }}" style="font-size: 18px;"></i>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    @if($contactUs->map_latitude && $contactUs->map_longitude)
    <section class="skc-section" style="background: var(--skc-light-gray);">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Find Us</h2>
                <p class="skc-section-subtitle">Visit our bakery and experience the magic in person</p>
            </div>
            
            <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 40px;">
                <div style="height: 500px; position: relative;">
                    <iframe 
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q={{ $contactUs->map_latitude }},{{ $contactUs->map_longitude }}&zoom=15"
                        style="width: 100%; height: 100%; border: 0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                @if($contactUs->map_address)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $contactUs->map_latitude }},{{ $contactUs->map_longitude }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   style="display: block; padding: 25px; background: linear-gradient(135deg, var(--skc-orange) 0%, #ff8c42 100%); color: white; text-decoration: none; transition: all 0.3s ease; cursor: pointer;"
                   onmouseover="this.style.background='linear-gradient(135deg, #ff8c42 0%, var(--skc-orange) 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 20px rgba(246, 157, 28, 0.3)';"
                   onmouseout="this.style.background='linear-gradient(135deg, var(--skc-orange) 0%, #ff8c42 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fas fa-map-marker-alt" style="font-size: 24px;"></i>
                            <div>
                                <h4 style="font-size: 18px; font-weight: 600; margin-bottom: 5px;">Our Location</h4>
                                <p style="margin: 0; opacity: 0.95;">{{ $contactUs->map_address }}</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; opacity: 0.9;">
                            <span style="font-size: 14px; font-weight: 500;">Open in Google Maps</span>
                            <i class="fas fa-external-link-alt" style="font-size: 14px;"></i>
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ Section -->
    @if($contactUs->faqs && count($contactUs->faqs) > 0)
    @php
        $activeFaqs = collect($contactUs->faqs)->filter(function($faq) {
            return isset($faq['is_active']) && $faq['is_active'];
        });
    @endphp
    
    @if($activeFaqs->count() > 0)
    <section class="skc-section">
        <div class="skc-container">
            <div class="skc-section-header">
                <h2 class="skc-section-title">Frequently Asked Questions</h2>
                <p class="skc-section-subtitle">Quick answers to common questions</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 40px;">
                @foreach($activeFaqs as $faq)
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                    <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px;">
                        <i class="fas fa-question-circle" style="color: var(--skc-orange); margin-right: 10px;"></i>
                        {{ $faq['question'] }}
                    </h3>
                    <p style="color: var(--skc-medium-gray); line-height: 1.6;">
                        {{ $faq['answer'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endif

    <style>
        /* Responsive styles for mobile devices */
        @media (max-width: 768px) {
            section.skc-section > .skc-container > div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
                gap: 40px !important;
            }
            
            div[style*="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr))"] {
                grid-template-columns: 1fr !important;
            }
        }
        
        /* Ensure iframe maps are responsive */
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
@endsection