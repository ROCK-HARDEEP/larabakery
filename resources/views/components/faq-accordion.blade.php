<!-- FAQ Section -->
<section class="skc-section" style="background: #f8f9fa; padding: 80px 0;">
    <div class="skc-container">
        <div class="skc-section-header" style="text-align: center; margin-bottom: 60px;">
            <h2 class="skc-section-title" style="font-size: 42px; font-weight: 700; color: var(--skc-black); margin: 0 0 20px 0;">
                Frequently Asked Questions
            </h2>
            <p class="skc-section-subtitle" style="font-size: 18px; color: var(--skc-medium-gray); max-width: 600px; margin: 0 auto; line-height: 1.6;">
                Find answers to common questions about our bakery, products, and services
            </p>
        </div>
        
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="faq-accordion" style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($faqs as $index => $faq)
                <div class="faq-item" style="background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; transition: all 0.3s ease;">
                    <button class="faq-question" 
                            onclick="toggleFaq('faq-{{ $index }}')" 
                            style="width: 100%; padding: 25px 30px; background: none; border: none; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s ease;">
                        <span style="font-size: 18px; font-weight: 600; color: var(--skc-black); flex: 1; margin-right: 20px;">
                            {{ $faq->question }}
                        </span>
                        <div class="faq-icon" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; background: var(--skc-orange); border-radius: 50%; color: white; font-size: 14px; transition: transform 0.3s ease;">
                            <i class="fas fa-plus"></i>
                        </div>
                    </button>
                    
                    <div id="faq-{{ $index }}" class="faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease, padding 0.3s ease;">
                        <div style="padding: 0 30px 25px 30px; font-size: 16px; line-height: 1.8; color: var(--skc-medium-gray); border-top: 1px solid #f0f0f0;">
                            {{ $faq->answer }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
.faq-item:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
}

.faq-question:hover {
    background: #fafafa !important;
}

.faq-item.active .faq-icon {
    transform: rotate(45deg);
    background: var(--skc-black) !important;
}

.faq-item.active .faq-answer {
    max-height: 500px !important;
    padding-top: 20px !important;
}

.faq-item.active .faq-question {
    background: linear-gradient(135deg, rgba(246,157,28,0.1) 0%, rgba(246,157,28,0.05) 100%) !important;
}

/* Animation for smooth expansion */
@keyframes expandFaq {
    from { 
        max-height: 0; 
        padding-top: 0; 
    }
    to { 
        max-height: 500px; 
        padding-top: 20px; 
    }
}

.faq-answer.expanding {
    animation: expandFaq 0.3s ease forwards;
}
</style>

<script>
function toggleFaq(faqId) {
    const faqItem = document.getElementById(faqId).closest('.faq-item');
    const faqAnswer = document.getElementById(faqId);
    const isActive = faqItem.classList.contains('active');
    
    // Close all other FAQs
    document.querySelectorAll('.faq-item.active').forEach(item => {
        if (item !== faqItem) {
            item.classList.remove('active');
            const answer = item.querySelector('.faq-answer');
            answer.style.maxHeight = '0';
            answer.style.paddingTop = '0';
        }
    });
    
    // Toggle current FAQ
    if (isActive) {
        faqItem.classList.remove('active');
        faqAnswer.style.maxHeight = '0';
        faqAnswer.style.paddingTop = '0';
    } else {
        faqItem.classList.add('active');
        faqAnswer.style.maxHeight = '500px';
        faqAnswer.style.paddingTop = '20px';
    }
}

// Initialize first FAQ as open
document.addEventListener('DOMContentLoaded', function() {
    const firstFaq = document.querySelector('.faq-item');
    if (firstFaq) {
        const firstFaqId = firstFaq.querySelector('.faq-answer').id;
        toggleFaq(firstFaqId);
    }
});
</script>