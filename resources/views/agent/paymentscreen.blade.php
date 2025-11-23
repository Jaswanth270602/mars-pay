@extends('agent.layout.header')
@section('content')

<div class="main-content-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-1">Complete Your Payment</h6>
                    <hr>
                    <iframe src="{{ $paymentLink }}" 
                            width="100%" 
                            height="700px" 
                            frameborder="0" 
                            style="border:0;">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
