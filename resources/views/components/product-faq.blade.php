<!-- Product FAQ Section -->
<section class="skc-section" style="background: white; padding: 60px 0;">
    <div class="skc-container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 50px;">
                <h3 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin: 0 0 15px 0; display: flex; align-items: center; justify-content: center; gap: 15px;">
                    <svg style="width: 32px; height: 32px; color: var(--skc-orange);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    Product FAQs
                </h3>
                <p style="font-size: 16px; color: var(--skc-medium-gray);">Get answers to common questions about this product</p>
            </div>
            
            @if($faqs->count() > 0)
                <div class="product-faq-accordion" style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($faqs as $index => $faq)
                    <div class="product-faq-item" style="background: #fafafa; border-radius: 12px; border: 1px solid #e5e5e5; overflow: hidden; transition: all 0.3s ease;">
                        <button class="product-faq-question" 
                                onclick="toggleProductFaq('product-faq-{{ $index }}')" 
                                style="width: 100%; padding: 20px 25px; background: none; border: none; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s ease;">
                            <span style="font-size: 16px; font-weight: 600; color: var(--skc-black); flex: 1; margin-right: 15px;">
                                {{ $faq->question }}
                            </span>
                            <div class="product-faq-icon" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; background: var(--skc-orange); border-radius: 50%; color: white; font-size: 12px; transition: transform 0.3s ease;">
                                <i class="fas fa-plus"></i>
                            </div>
                        </button>
                        
                        <div id="product-faq-{{ $index }}" class="product-faq-answer" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease, padding 0.3s ease;">
                            <div style="padding: 0 25px 20px 25px; font-size: 15px; line-height: 1.7; color: var(--skc-medium-gray); border-top: 1px solid #e0e0e0; background: white;">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px; background: #fafafa; border-radius: 12px; border: 1px solid #e5e5e5;">
                    <div style="font-size: 48px; color: #e0e0e0; margin-bottom: 15px;">
                        <i class="far fa-question-circle"></i>
                    </div>
                    <p style="font-size: 16px; color: var(--skc-medium-gray); margin: 0;">No FAQs available for this product yet.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
.product-faq-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
    border-color: var(--skc-orange) !important;
}

.product-faq-question:hover {
    background: #f0f0f0 !important;
}

.product-faq-item.active .product-faq-icon {
    transform: rotate(45deg);
    background: var(--skc-black) !important;
}

.product-faq-item.active .product-faq-answer {
    max-height: 400px !important;
    padding-top: 15px !important;
}

.product-faq-item.active .product-faq-question {
    background: linear-gradient(135deg, rgba(246,157,28,0.1) 0%, rgba(246,157,28,0.05) 100%) !important;
}

.product-faq-item.active {
    background: white !important;
    border-color: var(--skc-orange) !important;
    box-shadow: 0 6px 20px rgba(246,157,28,0.15) !important;
}
</style>

<script>
function toggleProductFaq(faqId) {
    const faqItem = document.getElementById(faqId).closest('.product-faq-item');
    const faqAnswer = document.getElementById(faqId);
    const isActive = faqItem.classList.contains('active');
    
    // Close all other FAQs
    document.querySelectorAll('.product-faq-item.active').forEach(item => {
        if (item !== faqItem) {
            item.classList.remove('active');
            const answer = item.querySelector('.product-faq-answer');
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
        faqAnswer.style.maxHeight = '400px';
        faqAnswer.style.paddingTop = '15px';
    }
}

// Initialize first FAQ as open if there are FAQs
document.addEventListener('DOMContentLoaded', function() {
    const firstProductFaq = document.querySelector('.product-faq-item');
    if (firstProductFaq) {
        const firstFaqId = firstProductFaq.querySelector('.product-faq-answer').id;
        toggleProductFaq(firstFaqId);
    }
});
</script>