@extends('front.template1.header')
@section('content')

<style>
/* Sections */
section { padding:50px 0; }
.container { max-width:1200px; margin:auto; padding:0 15px; }
.heading { text-align:center; margin-bottom:40px; }
.heading h3 { font-size:36px; font-weight:700; margin-bottom:10px; }
.heading p { font-size:16px; color:#555; }

/* Features */
.fea-gd-vv { display:flex; flex-wrap:wrap; justify-content:space-between; }
.feature-gd { flex:1; min-width:220px; text-align:center; background:#fff; margin:10px; padding:30px 20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); transition:0.3s; }
.feature-gd:hover { box-shadow:0 4px 16px rgba(0,0,0,0.2); }
.feature-gd .fa { font-size:36px; color:#007bff; margin-bottom:15px; }
.feature-gd h5 { font-size:18px; font-weight:600; }

/* About Section */
.cwp4-two { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; }
.cwp4-text { flex:1; min-width:300px; padding:20px; }
.cwp4-text h3 { font-size:28px; font-weight:700; margin-bottom:20px; }
.cwp4-text p { font-size:16px; margin-bottom:10px; line-height:1.6; }
.cwp4-image { flex:1; min-width:300px; padding:20px; }
.cwp4-image img { width:100%; border-radius:10px; }

/* Specifications */
.main-cont-wthree-fea { display:flex; flex-wrap:wrap; justify-content:space-between; }
.grids-speci1 { flex:1; min-width:220px; background:#fff; margin:10px; text-align:center; padding:30px 20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
.grids-speci1 .fa { font-size:36px; color:#007bff; margin-bottom:15px; }
.grids-speci1 h3 { font-size:24px; font-weight:700; margin-bottom:5px; }
.grids-speci1 p { font-size:16px; color:#555; }

/* Clients */
.customers_sur { background:#007bff; color:#fff; padding:50px 0; }
.customers_grid { margin-bottom:20px; }
.sub-test { font-size:16px; line-height:1.6; }
.customers-top_sur { display:flex; flex-wrap:wrap; justify-content:space-between; }

@media(max-width:768px){
    .fea-gd-vv, .main-cont-wthree-fea, .customers-top_sur, .cwp4-two { flex-direction:column; }
}
</style>

<!-- Features Section -->
<section>
    <div class="container fea-gd-vv">

        <div class="feature-gd">
            <span class="fa fa-user-plus"></span>
            <h5>Create your account</h5>
            <p>Sign up and set up your InfyPay dashboard in minutes.</p>
        </div>

        <div class="feature-gd">
            <span class="fa fa-list-alt"></span>
            <h5>Choose your plan</h5>
            <p>Select a subscription or service package that suits your business.</p>
        </div>

        <!-- ✅ Updated Step 3 (Option 3 Style) -->
        <div class="feature-gd">
            <span class="fa fa-shield-alt"></span>
            <h5>Secure Payment</h5>
            <p>Make your payment through our encrypted and highly secure gateway.</p>
        </div>

        <!-- ✅ Updated Step 4 (Option 3 Style) -->
        <div class="feature-gd">
            <span class="fa fa-smile-beam"></span>
            <h5>Get Activated</h5>
            <p>Instant activation—start using InfyPay services immediately.</p>
        </div>

    </div>
</section>


<!-- About Section -->
<section>
    <div class="container cwp4-two">
        <div class="cwp4-text">
            <h3>Welcome to {{ $company_name }}</h3>
            <p>{{ $company_name }} is an online Portal developed to create a B2B Business System for retailers.</p>
            <p>We provide services like Bill Payments (Electricity, Water, LPG), on-call solutions, and prompt responses.</p>
            <p>We are the best bill payment service and portal provider in PAN India.</p>
        </div>
        <div class="cwp4-image">
            <img src="{{url('front/images/about-img.png')}}" alt="About Image">
        </div>
    </div>
</section>

<!-- Specifications -->
<section>
    <div class="container main-cont-wthree-fea">
        <div class="grids-speci1">
            <span class="fa fa-heart"></span>
            <h3>40450</h3>
            <p>Our Clients</p>
        </div>
        <div class="grids-speci1">
            <span class="fa fa-thumbs-up"></span>
            <h3>13500</h3>
            <p>Packages Delivered</p>
        </div>
        <div class="grids-speci1">
            <span class="fa fa-address-card-o"></span>
            <h3>1500</h3>
            <p>Repeat Customers</p>
        </div>
        <div class="grids-speci1">
            <span class="fa fa-cog"></span>
            <h3>2000</h3>
            <p>Commercial Goods</p>
        </div>
    </div>
</section>

<!-- Clients Section -->
<section class="customers_sur">
    <div class="container">
        <div class="heading">
            <h3>Words From Our Clients</h3>
        </div>
        <div class="customers-top_sur">
            <div style="flex:1; min-width:300px; margin:10px;">
                <div class="customers_grid">
                    <p class="sub-test"><span class="fa fa-quote-left"></span>
                        I have never seen such a great quality. Good luck and keep it up! Thanks for their support which helps us grow our business.
                    </p>
                </div>
            </div>
            <div style="flex:1; min-width:300px; margin:10px;">
                <div class="customers_grid">
                    <p class="sub-test"><span class="fa fa-quote-left"></span>
                        Their quality services led us to achieve new growth in market. Thanks to webtech solution.net for outstanding support.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
