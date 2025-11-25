<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($company_name); ?></title>
<link href="//fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body { font-family: 'Karla', sans-serif; margin:0; padding:0; background:#f8f9fa; color:#333; }
a { text-decoration:none; color:inherit; }
ul { list-style:none; padding:0; margin:0; }
li { display:inline-block; margin-right:10px; }
/* Top Header */
.top-hd { background:#007bff; color:#fff; padding:8px 0; font-size:14px; }
.top-hd a { color:#fff; margin-left:5px; }
.social-top li a { color:#fff; margin-left:5px; font-size:14px; }
/* Navbar */
.navbar { display:flex; justify-content:space-between; align-items:center; background:#fff; padding:10px 20px; box-shadow:0 2px 8px rgba(0,0,0,0.1); position:sticky; top:0; z-index:999; }
.navbar-brand img { height:60px; }
.navbar-nav { display:flex; align-items:center; margin:0; padding:0; }
.navbar-nav li { margin:0 10px; }
.navbar-nav li a { padding:8px 12px; color:#333; font-weight:500; transition:0.3s; }
.navbar-nav li a:hover { color:#007bff; }
.btn-theme { padding:6px 15px; border-radius:4px; font-weight:bold; color:#fff; text-align:center; transition:0.3s; }
.btn-secondary { background:#6c757d; }
.btn-secondary:hover { background:#5a6268; }
.btn-primary { background:#007bff; margin-left:5px; }
.btn-primary:hover { background:#0056b3; }
/* Carousel */
.carousel-inner img { width:100%; height:400px; object-fit:cover; border-radius:8px; }
.carousel-control-prev-icon, .carousel-control-next-icon { background-color:#007bff; border-radius:50%; width:40px; height:40px; }
/* FOOTER */
.footer {
    background:#1e1e1e;
    color:#bbb;
    padding:40px 0 20px;
    margin-top:50px;
}
.footer h4 {
    color:#fff;
    font-size:18px;
    margin-bottom:15px;
}
.footer a {
    color:#bbb;
    display:block;
    margin:4px 0;
    font-size:14px;
    transition:0.3s;
}
.footer a:hover {
    color:#fff;
}
.footer .footer-logo img {
    height:60px;
}
.footer-bottom {
    text-align:center;
    color:#aaa;
    padding:10px 0;
    border-top:1px solid #444;
    font-size:14px;
}
@media(max-width:768px){
    .navbar-nav { flex-direction:column; }
    .navbar-nav li { margin:5px 0; }
}
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
    <div class="container" style="display:flex; justify-content:space-between;">
        <div>
            <li><span class="fa fa-mobile"></span><a href="tel:<?php echo e($whatsapp_number); ?>"><?php echo e($whatsapp_number); ?></a></li>
            <li><span class="fa fa-envelope"></span><?php echo e($company_email); ?></li>
        </div>
        <div class="social-top">
            <li><a href="#"><span class="fa fa-facebook"></span></a></li>
            <li><a href="#"><span class="fa fa-instagram"></span></a></li>
            <li><a href="#"><span class="fa fa-twitter"></span></a></li>
            <li><a href="#"><span class="fa fa-vimeo"></span></a></li>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar">
    <a class="navbar-brand" href="<?php echo e(url('')); ?>"><img src="<?php echo e($cdnLink); ?><?php echo e($company_logo); ?>"></a>
    <ul class="navbar-nav">
        <li><a href="<?php echo e(url('')); ?>">Home</a></li>
        <?php $__currentLoopData = App\Models\Navigation::where('status_id', 1)->where('company_id', $company_id)->where('type', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><a href="<?php echo e(url('pages')); ?>/<?php echo e($company_id); ?>/<?php echo e($value->navigation_slug); ?>"><?php echo e($value->navigation_name); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <li><a href="<?php echo e(url('contact-us')); ?>">Contact</a></li>
    </ul>
    <div>
        <a class="btn btn-secondary btn-theme" href="<?php echo e(url('login')); ?>">Login</a>
        <?php if($registration_status == 1): ?>
            <a class="btn btn-primary btn-theme" href="<?php echo e(url('sign-up')); ?>">Register</a>
        <?php endif; ?>
    </div>
</nav>

<!-- CONTENT AREA -->
<div class="content-wrapper pt-4 pb-4">
    <?php echo $__env->yieldContent('content'); ?>
</div>

<!-- Carousel -->
<section id="home">
    <div class="container">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php $__currentLoopData = $frontbanner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-item <?php echo e($key == 0 ? ' active' : ''); ?>">
                        <img src="<?php echo e($banner->banners); ?>" alt="Banner">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
    </div>
</section>

<!-- FOOTER SECTION -->
<footer class="footer">
    <div class="container" style="display:flex; flex-wrap:wrap; justify-content:space-between;">
        <!-- Logo & About -->
        <div class="footer-logo" style="flex:1; min-width:250px; margin-bottom:20px;">
            <img src="<?php echo e($cdnLink); ?><?php echo e($company_logo); ?>" alt="Logo">
            <p style="margin-top:10px; font-size:14px;">
                <?php echo e($company_name); ?> is a trusted payment solution platform providing fast, secure,
                and reliable services for all kinds of businesses.
            </p>
        </div>
        <!-- Quick Links -->
        <div style="flex:1; min-width:200px; margin-bottom:20px;">
            <h4>Quick Links</h4>
            <a href="<?php echo e(url('')); ?>">Home</a>
            <?php $__currentLoopData = App\Models\Navigation::where('status_id', 1)->where('company_id', $company_id)->where('type', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(url('pages')); ?>/<?php echo e($company_id); ?>/<?php echo e($value->navigation_slug); ?>"><?php echo e($value->navigation_name); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(url('contact-us')); ?>">Contact</a>
        </div>
        <!-- Contact Info -->
        <div style="flex:1; min-width:200px; margin-bottom:20px;">
            <h4>Contact Us</h4>
            <p><span class="fa fa-map-marker"></span> <?php echo e($company_address); ?></p>
            <p><span class="fa fa-envelope"></span> <?php echo e($company_email); ?></p>
            <p><span class="fa fa-phone"></span> <?php echo e($whatsapp_number); ?></p>
        </div>
        <!-- Social -->
        <div style="flex:1; min-width:180px; margin-bottom:20px;">
            <h4>Follow Us</h4>
            <a href="#"><span class="fa fa-facebook"></span> Facebook</a>
            <a href="#"><span class="fa fa-instagram"></span> Instagram</a>
            <a href="#"><span class="fa fa-twitter"></span> Twitter</a>
            <a href="#"><span class="fa fa-youtube"></span> YouTube</a>
        </div>
    </div>
    <div class="footer-bottom">
        © <?php echo e(date('Y')); ?> <?php echo e($company_name); ?> — All Rights Reserved.
    </div>
</footer>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

</body>
</html><?php /**PATH /var/www/infypay/resources/views/front/template1/header.blade.php ENDPATH**/ ?>