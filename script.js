document.addEventListener('DOMContentLoaded', function() {
    // Slider functionality
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    let currentIndex = 0;
    const totalSlides = slides.length;
    
    // Set up auto-sliding
    let slideInterval = setInterval(nextSlide, 5250);
    let typingTimeout = null;  // Store typing timeout

    function moveToSlide(index) {
        if (index < 0) {
            index = totalSlides - 1;
        } else if (index >= totalSlides) {
            index = 0;
        }
        
        slider.style.transform = `translateX(-${index * 100}%)`;
        currentIndex = index;
        
        // Clear any existing typing timeout before updating text
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }
        
        // Update typing text based on current slide
        updateTypingText(index);
        
        // Reset interval timer when manually changing slides
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5250);
    }
    
    function prevSlide() {
        moveToSlide(currentIndex - 1);
    }
    
    function nextSlide() {
        moveToSlide(currentIndex + 1);
    }
    
    // Set up event listeners for slider
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

    // Typing effect functionality
    const phrases = {
        1: "Quality Spices & Salts",
        2: "Trusted Since 1992      "
    };
    let currentPhraseIndex = 0;
    let currentCharIndex = 0;
    let isDeleting = false;
    let typingSpeed = 100;
    let deletingSpeed = 50;
    let pauseTime = 2000;

    const cursorElement = document.createElement('span');
    cursorElement.className = 'cursor';
    cursorElement.textContent = '|';
    cursorElement.style.animation = 'blink 1s infinite';

    const textContainer = document.createElement('div');
    textContainer.className = 'typing-text';
    textContainer.style.cssText = `
        position: absolute;
        bottom: 100px;
        right: 30px;
        font-family: 'Open Sans', sans-serif;
        font-size: 3.2em;
        color: #e21f26;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        text-align: right;
        z-index: 2;
        max-width: 80%;
        line-height: 1.2;
        padding: 15px 25px;
        background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
        border-radius: 8px;
        backdrop-filter: blur(5px);
    `;

    sliderContainer.appendChild(textContainer);
    textContainer.appendChild(cursorElement);

    function updateTypingText(slideIndex) {
        if (phrases[slideIndex]) {
            textContainer.style.display = 'block';
            currentPhraseIndex = slideIndex;
            currentCharIndex = 0;
            isDeleting = false;
            
            // Set different speeds based on the phrase
            if (slideIndex === 1) {
                typingSpeed = 100;  // Faster for "Quality Spices and Salts"
                deletingSpeed = 50;
            } else {
                typingSpeed = 150;  // Slower for "Trusted Since 1992"
                deletingSpeed = 75;
            }
            
            type();
        } else {
            textContainer.style.display = 'none';
        }
    }

    function type() {
        const currentPhrase = phrases[currentPhraseIndex];
        
        if (isDeleting) {
            textContainer.textContent = currentPhrase.substring(0, currentCharIndex - 1);
            currentCharIndex--;
            typingSpeed = deletingSpeed;
        } else {
            textContainer.textContent = currentPhrase.substring(0, currentCharIndex + 1);
            currentCharIndex++;
            typingSpeed = currentPhraseIndex === 1 ? 100 : 150;  // Different speeds for different phrases
        }

        if (!isDeleting && currentCharIndex === currentPhrase.length) {
            isDeleting = true;
            typingSpeed = pauseTime;
        } else if (isDeleting && currentCharIndex === 0) {
            isDeleting = false;
            typingSpeed = currentPhraseIndex === 1 ? 100 : 150;
        }

        // Store the timeout ID
        typingTimeout = setTimeout(type, typingSpeed);
    }

    // Add cursor blink animation to styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        .cursor {
            display: inline-block;
            width: 4px;
            height: 1.2em;
            background-color: #e21f26;
            margin-left: 4px;
            animation: blink 1s infinite;
            vertical-align: middle;
        }
        .typing-text {
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .typing-text::before {
            content: '';
            position: absolute;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #ffffff, #e21f26);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        .typing-text:hover::before {
            transform: scaleX(1);
        }
    `;
    document.head.appendChild(style);

    // Initialize typing text for first slide
    updateTypingText(currentIndex);

    // Mobile Menu Functionality
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mainNav = document.querySelector('.main-nav');
    const dropdowns = document.querySelectorAll('.dropdown');

    // Toggle mobile menu
    mobileMenuBtn.addEventListener('click', function() {
        this.classList.toggle('active');
        mainNav.classList.toggle('active');
    });

    // Handle dropdowns on mobile
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        const content = dropdown.querySelector('.dropdown-content');

        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            }
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            !mainNav.contains(e.target) && 
            !mobileMenuBtn.contains(e.target)) {
            mobileMenuBtn.classList.remove('active');
            mainNav.classList.remove('active');
        }
    });

    // Close mobile menu when resizing window
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mobileMenuBtn.classList.remove('active');
            mainNav.classList.remove('active');
            dropdowns.forEach(dropdown => {
                const content = dropdown.querySelector('.dropdown-content');
                content.style.display = 'none';
            });
        }
    });
});