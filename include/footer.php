    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="text-white mb-3"><i class="fas fa-bolt me-2" style="color: var(--primary);"></i>Vportal</h4>
                    <p class="text-muted">Your trusted destination for premium electric vehicles. Experience the future of transportation with our cutting-edge EV bikes.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="text-white mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="bikes.php" class="text-muted text-decoration-none">EV Bikes</a></li>
                        <li class="mb-2"><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="text-white mb-3">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">FAQs</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Warranty</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Service Centers</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Track Order</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 mb-4">
                    <h5 class="text-white mb-3">Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2" style="color: var(--primary);"></i> 123 EV Street, Tech City</li>
                        <li class="mb-2"><i class="fas fa-phone me-2" style="color: var(--primary);"></i> <?php echo getSetting('site_phone') ?: '+91 9876543210'; ?></li>
                        <li class="mb-2"><i class="fas fa-envelope me-2" style="color: var(--primary);"></i> <?php echo getSetting('site_email') ?: 'contact@evshowroom.com'; ?></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(0, 255, 136, 0.1);">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Vportal. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
