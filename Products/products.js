// Zoom_image code
document.addEventListener("DOMContentLoaded", function () {
    console.log("Script loaded!"); // Debugging

    const searchIcons = document.querySelectorAll(".icon.search");  // Fixed Selector
    const modal = document.getElementById("zoomModal");
    const modalImage = document.getElementById("zoomedImage");
    const closeModal = document.querySelector(".close-modal");

    // Ensure modal is hidden initially
    modal.style.display = "none";

    if (!searchIcons.length) {
        console.error("No search icons found! Check class names in HTML.");
    }

    searchIcons.forEach(icon => {
        icon.addEventListener("click", function (event) {
            event.preventDefault(); // Prevents reload

            const productImage = this.closest(".product-card").querySelector("img");
            if (productImage) {
                modalImage.src = productImage.src;
                modal.style.display = "flex"; // Open modal
                console.log("Modal Opened: " + productImage.src); // Debugging
            } else {
                console.error("Product image not found!");
            }
        });
    });

    closeModal.addEventListener("click", function () {
        modal.style.display = "none"; // Close modal
    });

    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Get all product cards
    const productCards = document.querySelectorAll('.product-card');
    
    // Get all category links
    const categoryLinks = document.querySelectorAll('.category-nav a');
    
    // Function to filter products based on category
    function filterProducts(category) {
        productCards.forEach(card => {
            if (category === 'ALL') {
                card.style.display = 'block';
                card.classList.add('animate__animated', 'animate__fadeIn');
            } else if (card.dataset.category === category) {
                card.style.display = 'block';
                card.classList.add('animate__animated', 'animate__fadeIn');
            } else {
                card.style.display = 'none';
                card.classList.remove('animate__animated', 'animate__fadeIn');
            }
        });
    }
    
    // Add click event listeners to category links
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            categoryLinks.forEach(item => item.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Filter products based on selected category
            filterProducts(this.textContent);
        });
    });
    
    // Initialize with all products shown
    filterProducts('ALL');
    
    // Add hover effects to product cards
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
    });
    
    // Handle link icon clicks
    const linkIcons = document.querySelectorAll('.icon.link');
    linkIcons.forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            const productName = this.closest('.overlay').querySelector('h3').textContent;
            alert(`Redirecting to ${productName} details...`);
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const closeMenuBtn = document.querySelector('.close-menu-btn');
    const mainNav = document.querySelector('.main-nav');
    const body = document.body;

    // Toggle mobile menu
    mobileMenuBtn.addEventListener('click', function() {
        mainNav.classList.add('active');
        body.classList.add('menu-open');
    });

    closeMenuBtn.addEventListener('click', function() {
        mainNav.classList.remove('active');
        body.classList.remove('menu-open');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mainNav.classList.contains('active') && 
            !mainNav.contains(event.target) && 
            event.target !== mobileMenuBtn) {
            mainNav.classList.remove('active');
            body.classList.remove('menu-open');
        }
    });

    // Handle dropdown menus
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        const content = dropdown.querySelector('.dropdown-content');

        link.addEventListener('click', function(e) {
            e.preventDefault();
            content.classList.toggle('active');
        });
    });
});
