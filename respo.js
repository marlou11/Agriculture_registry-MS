    
window.addEventListener("resize", handleResize);

// Check initial window size on page load
handleResize();

function handleResize() {
    const width = window.innerWidth;
    const nav = document.querySelector('nav');
    
    // For screens larger than 1024px (Desktops, MacBooks)
    if (width > 1024) {
        nav.style.fontSize = '18px';
    } 
    // For tablet sizes (1024px and below)
    else if (width <= 1024 && width > 768) {
        nav.style.fontSize = '16px';
    }
    // For mobile screens (768px and below)
    else if (width <= 768 && width > 480) {
        nav.style.fontSize = '14px';
    } 
    // For very small mobile devices (480px and below)
    else {
        nav.style.fontSize = '12px';
    }
}