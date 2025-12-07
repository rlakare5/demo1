<?php 
include 'include/config.php';
$pageTitle = 'Contact Us - Vportal';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $ticket_number = 'TKT' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        $query = "INSERT INTO support_tickets (ticket_number, subject, message, priority, status) 
                  VALUES ('$ticket_number', '$subject', '$message', 'medium', 'open')";
        
        if (mysqli_query($con, $query)) {
            $success = "Thank you for contacting us! Your ticket number is: $ticket_number";
        } else {
            $success = "Thank you for your message! We'll get back to you soon.";
        }
    }
}

include 'include/header.php';
?>

<div style="padding-top: 100px;"></div>

<section class="py-5">
    <div class="container">
        <h1 class="section-title">Contact <span>Us</span></h1>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card p-4 h-100">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Visit Us</h4>
                        <p class="text-muted">123 EV Street, Tech City<br>Innovation Hub, 560001</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4 h-100">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Call Us</h4>
                        <p class="text-muted"><?php echo getSetting('site_phone') ?: '+91 9876543210'; ?><br>Mon-Sat: 9AM - 7PM</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4 h-100">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email Us</h4>
                        <p class="text-muted"><?php echo getSetting('site_email') ?: 'contact@evshowroom.com'; ?><br>support@vportal.com</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8">
                <div class="card p-4">
                    <h3 class="mb-4">Send us a Message</h3>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php else: ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="What's this about?">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Your message..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
