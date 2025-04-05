document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    let currentIndex = 0;
    const totalSlides = slides.length;
    
    // Set up auto-sliding
    let slideInterval = setInterval(nextSlide, 5000);

    function moveToSlide(index) {
        if (index < 0) {
            index = totalSlides - 1;
        } else if (index >= totalSlides) {
            index = 0;
        }
        
        slider.style.transform = `translateX(-${index * 100}%)`;
        currentIndex = index;
        
        // Reset interval timer when manually changing slides
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
    }
    
    function prevSlide() {
        moveToSlide(currentIndex - 1);
    }
    
    function nextSlide() {
        moveToSlide(currentIndex + 1);
    }
    
    // Set up event listeners
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });

    // Add touch swipe support for mobile
    let startX;
    let endX;
    const sliderContainer = document.querySelector('.slider-container');

    sliderContainer.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
    }, false);

    sliderContainer.addEventListener('touchend', function(e) {
        endX = e.changedTouches[0].clientX;
        if (startX - endX > 50) {
            // Swipe left
            nextSlide();
        } else if (endX - startX > 50) {
            // Swipe right
            prevSlide();
        }
    }, false);
});