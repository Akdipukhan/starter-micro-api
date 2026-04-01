    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-broadcast-tower"></i> RADIO FM CUMILLA</h5>
                    <p><?php echo $settings['address']; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo $settings['contact_phone']; ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo $settings['contact_email']; ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/radio.php">Live Radio</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="<?php echo ADMIN_URL; ?>">Admin Panel</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="<?php echo $settings['facebook']; ?>"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo $settings['twitter']; ?>"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo $settings['youtube']; ?>"><i class="fab fa-youtube"></i></a>
                    </div>
                    
                    <h6 class="mt-4">Newsletter</h6>
                    <form action="#" method="POST" class="mt-2">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email">
                            <button class="btn btn-danger" type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="text-center">
                <p class="mb-0"><?php echo $settings['footer_text']; ?></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <script>
        // Dark Mode Toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('darkMode', 'disabled');
            }
        });

        // Check saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            const icon = document.querySelector('#darkModeToggle i');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    </script>
</body>
</html>
