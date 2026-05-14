document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const body = document.body;
    
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
            
            body.classList.toggle('menu-open');
        });
        
   
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
                body.classList.remove('menu-open');
            });
        });
        
      
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (navLinks.classList.contains('active') && 
                    !navLinks.contains(e.target) && 
                    !hamburger.contains(e.target)) {
                    navLinks.classList.remove('active');
                    hamburger.classList.remove('active');
                    body.classList.remove('menu-open');
                }
            }
        });
    }
    
 
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});

