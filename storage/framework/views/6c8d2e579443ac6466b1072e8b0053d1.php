<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($company_name); ?></title>
<link href="//fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Inter', sans-serif; background:#fff; color:#1a202c; overflow-x:hidden; }
a { text-decoration:none; color:inherit; transition:all 0.3s ease; }
ul { list-style:none; }
.container { max-width:1200px; margin:0 auto; padding:0 20px; }

/* Top Header - Modern Minimal */
.top-hd { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
    color:#fff; 
    padding:10px 0; 
    font-size:13px;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2);
}
.top-hd .container { display:flex; justify-content:space-between; align-items:center; }
.top-hd a { color:#fff; margin-left:8px; font-weight:500; }
.top-hd a:hover { opacity:0.8; }
.top-hd i { margin-right:5px; }
.social-top { display:flex; gap:15px; }
.social-top a { 
    width:32px; 
    height:32px; 
    background:rgba(255,255,255,0.15); 
    border-radius:50%; 
    display:flex; 
    align-items:center; 
    justify-content:center;
    backdrop-filter: blur(10px);
}
.social-top a:hover { background:rgba(255,255,255,0.3); transform:translateY(-2px); }

/* Navbar - Glassmorphism Effect */
.navbar { 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    background:rgba(255, 255, 255, 0.95); 
    backdrop-filter: blur(10px);
    padding:15px 20px; 
    box-shadow:0 4px 20px rgba(0,0,0,0.08); 
    position:sticky; 
    top:0; 
    z-index:999;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.navbar-brand img { height:55px; transition:transform 0.3s; }
.navbar-brand:hover img { transform:scale(1.05); }
.navbar-nav { display:flex; align-items:center; gap:5px; }
.navbar-nav li a { 
    padding:10px 18px; 
    color:#4a5568; 
    font-weight:600; 
    font-size:15px;
    border-radius:8px;
    position:relative;
}
.navbar-nav li a:hover { 
    color:#667eea; 
    background:rgba(102, 126, 234, 0.1);
}
.navbar-actions { display:flex; gap:12px; }
.btn-theme { 
    padding:10px 24px; 
    border-radius:10px; 
    font-weight:600; 
    font-size:14px;
    color:#fff; 
    border:none;
    cursor:pointer;
    transition:all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.btn-secondary { 
    background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
}
.btn-secondary:hover { 
    transform:translateY(-2px); 
    box-shadow: 0 6px 20px rgba(113, 128, 150, 0.3);
}
.btn-primary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.btn-primary:hover { 
    transform:translateY(-2px); 
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Hero Carousel - Modern & Bold */
.hero-section { 
    position:relative; 
    margin-top:0; 
    overflow:hidden;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.carousel-inner { border-radius:0; }
.carousel-inner img { 
    width:100%; 
    height:600px; 
    object-fit:cover;
    filter: brightness(0.9);
}
.carousel-item::after {
    content:'';
    position:absolute;
    top:0; left:0; right:0; bottom:0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
}
.carousel-control-prev, .carousel-control-next {
    width:60px;
    height:60px;
    background:rgba(255,255,255,0.9);
    border-radius:50%;
    top:50%;
    transform:translateY(-50%);
    opacity:1;
    transition:all 0.3s;
}
.carousel-control-prev { left:30px; }
.carousel-control-next { right:30px; }
.carousel-control-prev:hover, .carousel-control-next:hover {
    background:#fff;
    transform:translateY(-50%) scale(1.1);
}
.carousel-control-prev-icon, .carousel-control-next-icon { 
    background-color:#667eea;
    border-radius:50%; 
    width:30px; 
    height:30px;
}

/* Content Wrapper */
.content-wrapper { 
    min-height:400px; 
    padding:60px 0;
    background:#fff;
}

/* Features Section - Modern Cards */
.features-section {
    padding:80px 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
.features-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap:30px;
    margin-top:40px;
}
.feature-card {
    background:#fff;
    padding:40px 30px;
    border-radius:20px;
    text-align:center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition:all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}
.feature-card:hover {
    transform:translateY(-10px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
}
.feature-icon {
    width:80px;
    height:80px;
    margin:0 auto 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    color:#fff;
}
.feature-card h3 {
    font-size:22px;
    font-weight:700;
    margin-bottom:15px;
    color:#1a202c;
}
.feature-card p {
    color:#718096;
    line-height:1.7;
    font-size:15px;
}

/* Footer - Modern Dark */
.footer {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    color:#cbd5e0;
    padding:60px 0 30px;
    position:relative;
    overflow:hidden;
}
.footer::before {
    content:'';
    position:absolute;
    top:0; left:0; right:0;
    height:4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}
.footer-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap:40px;
    margin-bottom:40px;
}
.footer h4 {
    color:#fff;
    font-size:20px;
    font-weight:700;
    margin-bottom:20px;
    position:relative;
    padding-bottom:10px;
}
.footer h4::after {
    content:'';
    position:absolute;
    bottom:0; left:0;
    width:40px; height:3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}
.footer a {
    color:#cbd5e0;
    display:block;
    margin:10px 0;
    font-size:15px;
    transition:all 0.3s;
    padding-left:0;
}
.footer a:hover {
    color:#fff;
    padding-left:10px;
}
.footer-logo img {
    height:60px;
    margin-bottom:20px;
    filter: brightness(1.2);
}
.footer-logo p {
    line-height:1.8;
    margin-top:15px;
    font-size:14px;
}
.footer-bottom {
    text-align:center;
    color:#a0aec0;
    padding:25px 0 0;
    border-top:1px solid rgba(255,255,255,0.1);
    font-size:14px;
    margin-top:40px;
}
.footer-social {
    display:flex;
    gap:15px;
    margin-top:20px;
}
.footer-social a {
    width:40px;
    height:40px;
    background:rgba(255,255,255,0.1);
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}
.footer-social a:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform:translateY(-3px);
    padding-left:0;
}

/* Mobile Responsive */
@media(max-width:768px){
    .navbar { flex-wrap:wrap; }
    .navbar-nav { 
        flex-direction:column; 
        width:100%; 
        margin-top:15px;
        display:none;
    }
    .navbar-nav.active { display:flex; }
    .navbar-nav li { width:100%; text-align:center; }
    .carousel-inner img { height:300px; }
    .features-grid { grid-template-columns: 1fr; }
    .top-hd .container { flex-direction:column; gap:10px; text-align:center; }
}

/* Smooth Animations */
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(30px); }
    to { opacity:1; transform:translateY(0); }
}
.feature-card { animation: fadeInUp 0.6s ease-out; }
</style>
</head>
<body>


<?php if(Auth::guest()): ?>
    
<?php else: ?>
    
    <?php if(Auth::user()->role_id <= 7): ?>
        <script type="text/javascript">
            window.location.href = "<?php echo e(url('admin/dashboard')); ?>";
        </script>
    <?php else: ?>
        <script type="text/javascript">
            window.location.href = "<?php echo e(url('agent/dashboard')); ?>";
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php echo $chat_script; ?>


<!-- Top Header -->
<div class="top-hd">
    <div class="container">
        <div>
            <span><i class="fas fa-phone-alt"></i><a href="tel:<?php echo e($whatsapp_number); ?>"><?php echo e($whatsapp_number); ?></a></span>
            <span style="margin-left:20px;"><i class="fas fa-envelope"></i><a href="mailto:<?php echo e($company_email); ?>"><?php echo e($company_email); ?></a></span>
        </div>
        <div class="social-top">
            <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar">
    <a class="navbar-brand" href="<?php echo e(url('')); ?>">
        <img src="<?php echo e($cdnLink); ?><?php echo e($company_logo); ?>" alt="<?php echo e($company_name); ?>">
    </a>
    <ul class="navbar-nav">
        <li><a href="<?php echo e(url('')); ?>"><i class="fas fa-home"></i> Home</a></li>
        <?php $__currentLoopData = App\Models\Navigation::where('status_id', 1)->where('company_id', $company_id)->where('type', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><a href="<?php echo e(url('pages')); ?>/<?php echo e($company_id); ?>/<?php echo e($value->navigation_slug); ?>"><?php echo e($value->navigation_name); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <li><a href="<?php echo e(url('contact-us')); ?>"><i class="fas fa-envelope"></i> Contact</a></li>
    </ul>
    <div class="navbar-actions">
        <a class="btn btn-secondary btn-theme" href="<?php echo e(url('login')); ?>">
            <i class="fas fa-sign-in-alt"></i> Login
        </a>
        <?php if($registration_status == 1): ?>
            <a class="btn btn-primary btn-theme" href="<?php echo e(url('sign-up')); ?>">
                <i class="fas fa-user-plus"></i> Register
            </a>
        <?php endif; ?>
    </div>
</nav>

<!-- Hero Carousel -->
<section class="hero-section">
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="5000">
        <div class="carousel-inner">
            
           <?php if(!empty($frontbanner)): ?>
                <?php $__currentLoopData = $frontbanner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-item <?php echo e($key == 0 ? ' active' : ''); ?>">
                        <img src="<?php echo e($banner->banners); ?>" alt="Banner">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div style="text-align:center; margin-bottom:20px;">
            <h2 style="font-size:40px; font-weight:800; color:#1a202c; margin-bottom:15px;">Why Choose Us</h2>
            <p style="color:#718096; font-size:18px; max-width:600px; margin:0 auto;">Fast, secure, and reliable payment solutions for your business</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Lightning Fast</h3>
                <p>Process transactions in seconds with our optimized payment infrastructure</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>100% Secure</h3>
                <p>Bank-level encryption and security protocols to protect your data</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Round-the-clock customer support to help you whenever you need</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENT AREA -->
<div class="content-wrapper">
    <div class="container">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Company Info -->
            <div>
                <div class="footer-logo">
                    <img src="<?php echo e($cdnLink); ?><?php echo e($company_logo); ?>" alt="<?php echo e($company_name); ?>">
                </div>
                <p><?php echo e($company_name); ?> is a trusted payment solution platform providing fast, secure, and reliable services for all kinds of businesses.</p>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4>Quick Links</h4>
                <a href="<?php echo e(url('')); ?>">Home</a>
                <?php $__currentLoopData = App\Models\Navigation::where('status_id', 1)->where('company_id', $company_id)->where('type', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(url('pages')); ?>/<?php echo e($company_id); ?>/<?php echo e($value->navigation_slug); ?>"><?php echo e($value->navigation_name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(url('contact-us')); ?>">Contact Us</a>
            </div>
            
            <!-- Legal -->
            <div>
                <h4>Legal</h4>
                <?php $__currentLoopData = App\Models\Navigation::where('status_id', 1)->where('company_id', $company_id)->where('type', 2)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(url('pages')); ?>/<?php echo e($company_id); ?>/<?php echo e($value->navigation_slug); ?>"><?php echo e($value->navigation_name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <!-- Contact -->
            <div>
                <h4>Contact Us</h4>
                <p style="display:flex; align-items:start; margin:12px 0;">
                    <i class="fas fa-map-marker-alt" style="margin-right:10px; margin-top:4px;"></i>
                    <span><?php echo e($company_address ?? 'Your Business Address'); ?></span>
                </p>
                <p style="display:flex; align-items:center; margin:12px 0;">
                    <i class="fas fa-envelope" style="margin-right:10px;"></i>
                    <span><?php echo e($company_email); ?></span>
                </p>
                <p style="display:flex; align-items:center; margin:12px 0;">
                    <i class="fas fa-phone-alt" style="margin-right:10px;"></i>
                    <span><?php echo e($whatsapp_number); ?></span>
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            © <?php echo e(date('Y')); ?> <?php echo e($company_name); ?>. All Rights Reserved. | Made with <i class="fas fa-heart" style="color:#e53e3e;"></i> in India
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

</body>
</html><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/front/template1/header.blade.php ENDPATH**/ ?>