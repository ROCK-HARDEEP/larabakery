// Initialize all carousels
document.addEventListener('DOMContentLoaded', function() {
    
    // Categories Carousel
    if (document.querySelector('.categories-swiper')) {
        const categoriesSwiper = new Swiper('.categories-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 1, // Minimal delay for continuous scrolling
                disableOnInteraction: false,
                pauseOnMouseEnter: false,
                reverseDirection: false
            },
            speed: 5000, // Slow, smooth scrolling speed
            freeMode: {
                enabled: true,
                sticky: false
            },
            pagination: {
                el: '.categories-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // Mobile: 2 items
                320: {
                    slidesPerView: 2,
                    spaceBetween: 15
                },
                // Tablet: 4 items
                768: {
                    slidesPerView: 4,
                    spaceBetween: 20
                },
                // Desktop: 6 items
                1024: {
                    slidesPerView: 6,
                    spaceBetween: 30
                }
            }
        });
        
        // Manual hover pause for categories carousel
        const categoriesContainer = document.querySelector('.categories-swiper');
        if (categoriesContainer && categoriesSwiper.autoplay) {
            let isPaused = false;
            
            // Start autoplay initially
            categoriesSwiper.autoplay.start();
            
            categoriesContainer.addEventListener('mouseenter', function(e) {
                if (!isPaused) {
                    categoriesSwiper.autoplay.stop();
                    isPaused = true;
                }
            });
            
            categoriesContainer.addEventListener('mouseleave', function(e) {
                if (isPaused) {
                    categoriesSwiper.autoplay.start();
                    isPaused = false;
                }
            });
        }
    }

    // Features (Why Choose Us) Carousel
    if (document.querySelector('.features-swiper')) {
        const featuresSwiper = new Swiper('.features-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 1, // Minimal delay for continuous scrolling
                disableOnInteraction: false,
                pauseOnMouseEnter: false,
                reverseDirection: false
            },
            speed: 6000, // Slightly slower for features
            freeMode: {
                enabled: true,
                sticky: false
            },
            pagination: {
                el: '.features-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // Mobile: 2 items
                320: {
                    slidesPerView: 2,
                    spaceBetween: 15
                },
                // Tablet: 3 items
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20
                },
                // Desktop: 4 items
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            }
        });
        
        // Manual hover pause for features carousel
        const featuresContainer = document.querySelector('.features-swiper');
        if (featuresContainer && featuresSwiper.autoplay) {
            let isPaused = false;
            
            // Start autoplay initially
            featuresSwiper.autoplay.start();
            
            featuresContainer.addEventListener('mouseenter', function(e) {
                if (!isPaused) {
                    featuresSwiper.autoplay.stop();
                    isPaused = true;
                }
            });
            
            featuresContainer.addEventListener('mouseleave', function(e) {
                if (isPaused) {
                    featuresSwiper.autoplay.start();
                    isPaused = false;
                }
            });
        }
    }

    // Values Carousel (About Page)
    if (document.querySelector('.values-swiper')) {
        const valuesSwiper = new Swiper('.values-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 1, // Minimal delay for continuous scrolling
                disableOnInteraction: false,
                pauseOnMouseEnter: false,
                reverseDirection: false
            },
            speed: 7000, // Slower for values to read easily
            freeMode: {
                enabled: true,
                sticky: false
            },
            pagination: {
                el: '.values-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // Mobile: 2 items
                320: {
                    slidesPerView: 2,
                    spaceBetween: 15
                },
                // Tablet: 3 items
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20
                },
                // Desktop: 4 items
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            }
        });
        
        // Manual hover pause for values carousel
        const valuesContainer = document.querySelector('.values-swiper');
        if (valuesContainer && valuesSwiper.autoplay) {
            let isPaused = false;
            
            // Start autoplay initially
            valuesSwiper.autoplay.start();
            
            valuesContainer.addEventListener('mouseenter', function(e) {
                if (!isPaused) {
                    valuesSwiper.autoplay.stop();
                    isPaused = true;
                }
            });
            
            valuesContainer.addEventListener('mouseleave', function(e) {
                if (isPaused) {
                    valuesSwiper.autoplay.start();
                    isPaused = false;
                }
            });
        }
    }

    // Team Carousel (About Page)
    if (document.querySelector('.team-swiper')) {
        // Count team members to determine optimal desktop slides
        const teamSlides = document.querySelectorAll('.team-swiper .swiper-slide');
        const teamCount = teamSlides.length;
        
        // Determine desktop slides per view based on team count
        let desktopSlidesPerView = 4; // Default for 4+ members
        if (teamCount <= 3) {
            desktopSlidesPerView = teamCount; // Show all members if 3 or fewer
        }
        
        const teamSwiper = new Swiper('.team-swiper', {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: teamCount > 3, // Only loop if more than 3 members
            autoplay: teamCount > 3 ? {
                delay: 0, // Continuous scrolling
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
                reverseDirection: false
            } : false, // No autoplay if 3 or fewer members
            speed: teamCount > 3 ? 8000 : 800, // Slower for team members
            freeMode: teamCount > 3 ? {
                enabled: true,
                sticky: false
            } : false,
            pagination: {
                el: '.team-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // Mobile: 2 items
                320: {
                    slidesPerView: 2,
                    spaceBetween: 15
                },
                // Tablet: 3 items
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20
                },
                // Desktop: Dynamic based on team count
                1024: {
                    slidesPerView: desktopSlidesPerView,
                    spaceBetween: 30
                }
            }
        });
        
        // Manual hover pause for team carousel if autoplay is enabled
        if (teamCount > 3) {
            const teamContainer = document.querySelector('.team-swiper');
            if (teamContainer && teamSwiper.autoplay) {
                let isPaused = false;
                
                // Start autoplay initially
                teamSwiper.autoplay.start();
                
                teamContainer.addEventListener('mouseenter', function(e) {
                    if (!isPaused) {
                        teamSwiper.autoplay.stop();
                        isPaused = true;
                    }
                });
                
                teamContainer.addEventListener('mouseleave', function(e) {
                    if (isPaused) {
                        teamSwiper.autoplay.start();
                        isPaused = false;
                    }
                });
            }
        }
    }
});