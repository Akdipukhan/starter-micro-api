// Radio FM Cumilla - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Lazy loading images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Back to top button
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTop.className = 'btn btn-danger btn-lg';
    backToTop.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: none;
        z-index: 1000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
    `;
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'block';
        } else {
            backToTop.style.display = 'none';
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Search autocomplete (basic implementation)
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;
            
            if (query.length >= 3) {
                timeout = setTimeout(() => {
                    // AJAX search suggestions would go here
                    console.log('Searching for:', query);
                }, 300);
            }
        });
    }

    // Comment character counter
    const commentTextarea = document.querySelector('textarea[name="comment"]');
    if (commentTextarea) {
        const counter = document.createElement('small');
        counter.className = 'text-muted float-end';
        commentTextarea.parentNode.appendChild(counter);
        
        commentTextarea.addEventListener('input', function() {
            const remaining = 1000 - this.value.length;
            counter.textContent = `${remaining} characters remaining`;
            
            if (remaining < 100) {
                counter.className = 'text-warning float-end';
            }
            if (remaining < 0) {
                counter.className = 'text-danger float-end';
            }
        });
    }

    // Audio player visualizer (if present)
    const audioPlayer = document.querySelector('audio');
    if (audioPlayer) {
        audioPlayer.addEventListener('play', function() {
            console.log('Audio playing');
        });
        
        audioPlayer.addEventListener('pause', function() {
            console.log('Audio paused');
        });
        
        audioPlayer.addEventListener('error', function(e) {
            console.error('Audio error:', e);
            alert('Unable to load audio stream. Please try again later.');
        });
    }

    // Prevent double form submission
    const forms_submit = document.querySelectorAll('form');
    forms_submit.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.innerHTML.replace('<i class="fas fa-spinner fa-spin"></i> Processing...', 'Submit');
                }, 10000);
            }
        });
    });

    // Session timeout warning (for admin panel)
    if (typeof SESSION_TIMEOUT !== 'undefined') {
        setTimeout(() => {
            if (confirm('Your session is about to expire. Do you want to continue?')) {
                // Refresh session via AJAX
                location.reload();
            }
        }, SESSION_TIMEOUT - 60000); // Warn 1 minute before timeout
    }

    console.log('Radio FM Cumilla initialized successfully!');
});

// Utility functions

/**
 * Format date in Bangla
 */
function formatDateBangla(dateString) {
    const months = [
        'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
        'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'
    ];
    
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    
    return `${day} ${month}, ${year}`;
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Get time ago in Bangla
 */
function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit} ago`;
        }
    }
    
    return 'Just now';
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}

/**
 * Share on social media
 */
function shareOnSocial(platform, url, title) {
    const urls = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
        twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
        whatsapp: `https://wa.me/?text=${encodeURIComponent(title + ' - ' + url)}`,
        linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`
    };
    
    if (urls[platform]) {
        window.open(urls[platform], '_blank', 'width=600,height=400');
    }
}

/**
 * Toggle dark mode
 */
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
}

/**
 * Print article
 */
function printArticle() {
    window.print();
}

/**
 * Report inappropriate content
 */
function reportContent(type, id) {
    if (confirm('Do you want to report this content as inappropriate?')) {
        // Send report via AJAX
        console.log('Reporting:', type, id);
        alert('Thank you for your report. We will review it shortly.');
    }
}
