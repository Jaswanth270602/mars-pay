<?php $__env->startSection('content'); ?>

<style>
/* ------------------------------
   CONTACT PAGE BEAUTIFUL DESIGN
--------------------------------*/

/* Inner Banner */
.w3l-inner-banner-main {
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                url('../images/banner.jpg') center/cover no-repeat;
    padding: 80px 0;
    color: #fff;
}
.w3l-inner-banner-main .breadcrumbs-custom-path li {
    color: #fff;
    font-size: 15px;
}
.breadcrumbs-custom-path a {
    color: #fff;
    transition: 0.3s;
}
.breadcrumbs-custom-path a:hover {
    color: #ffd700;
}

/* Heading */
.heading .head {
    font-size: 32px;
    font-weight: 700;
    color: #222;
}

/* Contact Section */
.w3l-contacts-12 {
    background: #f8f9fc;
    padding-bottom: 70px;
}

.contacts12-main form .form-input {
    width: 100%;
    padding: 14px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: #fff;
    margin-bottom: 20px;
    transition: 0.3s;
    font-size: 15px;
}

.contacts12-main form .form-input:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 5px rgba(74,144,226,0.3);
}

/* Submit Button */
.btn-theme2 {
    background: #4a90e2;
    padding: 12px 30px;
    border-radius: 30px;
    color: #fff;
    font-weight: 600;
    transition: 0.3s;
}
.btn-theme2:hover {
    background: #1e73d0;
    color: #fff;
}

/* Contact Info Box */
.contact .cont-subs {
    background: #fff;
    border-radius: 12px;
    padding: 35px 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.contact h5 {
    font-size: 26px;
    font-weight: 600;
}

.cont-add {
    display: flex;
    margin-bottom: 25px;
    align-items: flex-start;
}
.cont-add-lft {
    width: 45px;
    height: 45px;
    background: #4a90e2;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}
.cont-add-lft span {
    color: #fff;
    font-size: 18px;
}
.cont-add-rgt h4 {
    margin: 0;
    margin-left: 12px;
    font-size: 18px;
    color: #333;
}
.cont-add-rgt p,
.cont-add-rgt a {
    margin-left: 12px;
    color: #555;
    font-size: 15px;
}

@media (max-width: 767px) {
    .contact .cont-subs {
        margin-top: 40px;
    }
    .heading .head {
        font-size: 26px;
    }
}
</style>


    <!-- inner banner -->
    <section class="w3l-inner-banner-main">
        <div class="about-inner">
            <div class="container">
                <ul class="breadcrumbs-custom-path">
                    <li><a href="index.php">Home <span class="fa fa-angle-double-right" aria-hidden="true"></span></a></li>
                    <li class="active">Contact</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- contact -->
    <section class="w3l-contacts-12" id="contact">
        <div class="contact-top pt-5">
            <div class="container py-md-3">
                <div class="heading text-center mx-auto">
                    <h3 class="head">Have you a question?</h3>
                    <p class="my-3 head"> </p>
                </div>

                <div class="row cont-main-top mt-5 pt-3">

                    <div class="contacts12-main col-lg-7 pr-lg-5 pr-3">
                        <?php if(Session::has('success')): ?>
                            <div class="alert alert-info">
                                <a class="close" data-dismiss="alert">×</a>
                                <strong>Alert </strong> <?php echo Session::get('success'); ?>

                            </div>
                        <?php endif; ?>
                        <form role="form" action="<?php echo e(url('save-contact-enquiry')); ?>" method="post" class="main-input">
                            <?php echo csrf_field(); ?>

                            <div class="top-inputs">
                                <input type="text" name="name" class="form-input" value="<?php echo e(old('name')); ?>" placeholder="Your Name"/>
                                <?php if($errors->has('name')): ?>
                                    <span style="color: red; font-size: 12px; margin-top: 5%;"><?php echo e($errors->first('name')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="top-inputs">
                                <input type="email" name="email" class="form-input" value="<?php echo e(old('email')); ?>" placeholder="Email" />
                                <?php if($errors->has('email')): ?>
                                    <span style="color: red; font-size: 12px; margin-top: 5%;"><?php echo e($errors->first('email')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="top-inputs">
                                <input type="tel" name="mobile_number" class="form-input" value="<?php echo e(old('mobile_number')); ?>" placeholder="Phone / Mobile No." />
                                <?php if($errors->has('mobile_number')): ?>
                                    <span style="color: red; font-size: 12px; margin-top: 5%;"><?php echo e($errors->first('mobile_number')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="top-inputs">
                                <input type="text" name="message" class="form-input" value="<?php echo e(old('message')); ?>" placeholder="Message" />
                                <?php if($errors->has('message')): ?>
                                    <span style="color: red; font-size: 12px; margin-top: 5%;"><?php echo e($errors->first('message')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-theme2">Submit Now</button>
                            </div>
                        </form>
                    </div>

                    <div class="contact col-lg-5 mt-lg-0 mt-5">
                        <div class="cont-subs">
                            <h5>Contact Info</h5>
                            <p class="mt-3">Have any Queries? Let us know. We will clear it for you at the best.</p>

                            <div class="cont-add mt-4">
                                <div class="cont-add-lft">
                                    <span class="fa fa-map-marker" aria-hidden="true"></span>
                                </div>
                                <div class="cont-add-rgt">
                                    <h4>Office</h4>
                                    <p class="contact-text-sub"><?php echo e($company_address); ?></p>
                                </div>
                            </div>

                            <div class="cont-add mt-4">
                                <div class="cont-add-lft">
                                    <span class="fa fa-map-marker" aria-hidden="true"></span>
                                </div>
                                <div class="cont-add-rgt">
                                    <h4>Office 2</h4>
                                    <p class="contact-text-sub"><?php echo e($company_address_two); ?></p>
                                </div>
                            </div>

                            <div class="cont-add my-4">
                                <div class="cont-add-lft">
                                    <span class="fa fa-envelope" aria-hidden="true"></span>
                                </div>
                                <div class="cont-add-rgt">
                                    <h4>Email</h4>
                                    <a href="mailto:<?php echo e($company_email); ?>">
                                        <p class="contact-text-sub"><?php echo e($company_email); ?></p>
                                    </a>
                                </div>
                            </div>

                            <div class="cont-add my-4">
                                <div class="cont-add-lft">
                                    <span class="fa fa-phone" aria-hidden="true"></span>
                                </div>
                                <div class="cont-add-rgt">
                                    <h4>Support Number</h4>
                                    <a href="tel:<?php echo e($support_number); ?>">
                                        <p class="contact-text-sub"><?php echo e($support_number); ?></p>
                                    </a>
                                </div>
                            </div>

                            <div class="cont-add">
                                <div class="cont-add-lft">
                                    <span class="fa fa-phone" aria-hidden="true"></span>
                                </div>
                                <div class="cont-add-rgt">
                                    <h4>Whatsapp Number</h4>
                                    <a href="tel:<?php echo e($whatsapp_number); ?>">
                                        <p class="contact-text-sub"><?php echo e($whatsapp_number); ?></p>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.template1.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\pc\Desktop\mars-pay\resources\views/front/template1/contact_us.blade.php ENDPATH**/ ?>