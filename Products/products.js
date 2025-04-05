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
// document.addEventListener('DOMContentLoaded', function() {
//     const scrollContainers = document.querySelectorAll('.scroll');
    
//     // Product descriptions mapping
//     const productDescriptions = {
//         'Garam.png': {
//             title: 'Goldiee Garam Masala',
//             description: 'A perfect blend of hand-picked spices, our Garam Masala adds warmth and depth to your dishes. Crafted to elevate the flavors of Indian cuisine.'
//         },
//         // Add more product image mappings here
//         'default': {
//             title: 'Goldiee Product',
//             description: 'Discover the rich flavors of our premium spice collection.'
//         }
//     };

//     scrollContainers.forEach(container => {
//         const images = container.querySelectorAll('img');
        
//         images.forEach(img => {
//             // Create description overlay
//             const overlay = document.createElement('div');
//             overlay.classList.add('product-description-overlay');
//             overlay.style.cssText = `
//                 position: absolute;
//                 top: 0;
//                 left: 0;
//                 width: 100%;
//                 height: 100%;
//                 background: rgba(0,0,0,0.7);
//                 color: white;
//                 display: flex;
//                 flex-direction: column;
//                 justify-content: center;
//                 align-items: center;
//                 opacity: 0;
//                 transition: opacity 0.3s ease;
//                 padding: 10px;
//                 box-sizing: border-box;
//                 text-align: center;
//                 z-index: 10;
//             `;

//             // Container for the image to allow positioning
//             const imageWrapper = document.createElement('div');
//             imageWrapper.style.cssText = `
//                 position: relative;
//                 display: inline-block;
//             `;

//             // Wrap the image
//             img.parentNode.insertBefore(imageWrapper, img);
//             imageWrapper.appendChild(img);

//             // Add event listeners
//             imageWrapper.addEventListener('mouseenter', () => {
//                 // Get description (use filename or default)
//                 const productInfo = productDescriptions[img.getAttribute('src').split('/').pop()] || productDescriptions['default'];
                
//                 // Update overlay content
//                 overlay.innerHTML = `
//                     <h4 style="margin-bottom: 10px; font-size: 1.2em; font-weight: bold;">${productInfo.title}</h4>
//                     <p style="font-size: 0.9em; max-width: 200px;">${productInfo.description}</p>
//                 `;

//                 // Append overlay
//                 imageWrapper.appendChild(overlay);
                
//                 // Fade in
//                 overlay.style.opacity = '1';
//             });

//             imageWrapper.addEventListener('mouseleave', () => {
//                 // Fade out and remove
//                 overlay.style.opacity = '0';
//                 setTimeout(() => {
//                     if (overlay.parentNode === imageWrapper) {
//                         imageWrapper.removeChild(overlay);
//                     }
//                 }, 300);
//             });
//         });
//     });
// });
