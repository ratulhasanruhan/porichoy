@extends("user.$folder.layout")
@section('styles')
@endsection
@section('tab-title')
    {{ $keywords['Payment'] ?? 'Payment' }}
@endsection
@section('br-title')
    {{ $keywords['Payment'] ?? 'Payment' }}
@endsection
@section('br-link')
    {{ $keywords['Payment'] ?? 'Payment' }}
@endsection
@section('content')
    @if ($userBs->theme == 6 || $userBs->theme == 7 || $userBs->theme == 8)
        <!--====== Breadcrumbs Start ======-->
        <section class="breadcrumbs-section">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-10">
                        <div class="page-title">
                            <h1>{{ $keywords['Checkout'] ?? 'Checkout' }}</h1>
                            <ul class="breadcrumbs-link">
                                <li><a
                                        href="{{ route('front.user.detail.view', getParam()) }}">{{ $keywords['Home'] ?? 'Home' }}</a>
                                </li>
                                <li class="">{{ $keywords['Checkout'] ?? 'Checkout' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--====== Breadcrumbs End ======-->
    @endif
    @php
        $appointment_summary = Session::get('user_request');
        $dt = Carbon\carbon::parse($appointment_summary['date']);
    @endphp
    <!--====== PROFILE PART START ======-->
    <section class="dashboard-area">
        <div class="container">
            <form action="{{ route('front.user.appointment.checkout', getParam()) }}" method="POST"
                enctype="multipart/form-data" id="my-checkout-form">
                @csrf
                <div class="row">
                    <div class="col-lg-6 ">
                        <div class="card-body shadow-sm p-4 mt-30 bg-white rounded">
                            <div class="order_wrap_box">
                                <div class="order_payment_box">
                                    <h3 class="mb-3">{{ $keywords['Appointment_summary'] ?? 'Appointment Summary' }}</h3>
                                    <div class="form_group">
                                        <span>
                                            <Strong>{{ $keywords['Name'] ?? 'Name' }} : </Strong>
                                            {{ $appointment_summary['name'] }}
                                        </span> <br>
                                        <span>
                                            <Strong>{{ $keywords['Email'] ?? 'Email' }} : </Strong>
                                            {{ $appointment_summary['email'] }}
                                        </span> <br>
                                        <span>
                                            <Strong>{{ $keywords['Booking_Date'] ?? 'Booking Date' }} : </Strong>
                                            {{ $dt->isoFormat('DD MMMM YYYY') }}
                                        </span> <br>
                                        <span>
                                            <Strong>{{ $keywords['Booking_Time'] ?? 'Booking Time' }} : </Strong>
                                            {{ $appointment_summary['slot'] }}
                                        </span> <br>
                                        @if (!empty($appointment_summary['category_id']))
                                            <span>
                                                <Strong>{{ $keywords['Catgory'] ?? 'Catgory' }} : </Strong>
                                                @php
                                                    $catg = App\Models\User\Category::where('id', $appointment_summary['category_id'])
                                                        ->where('language_id', $userCurrentLang->id)
                                                        ->where('user_id', getUser()->id)
                                                        ->first()->name;
                                                @endphp
                                                {{ $catg }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 ">
                        <div class="card-body shadow-sm p-4 mt-30 bg-white rounded">
                            <div class="order_wrap_box">
                                <div class="order_payment_box">
                                    <h3 class="mb-3">{{ $keywords['Payment_Method'] ?? 'Payment Method' }}</h3>
                                    <div class="form_group">
                                        <span><Strong>{{ $keywords['Total_Fee'] ?? 'Total Fee' }} : </Strong>
                                            {{ $userBs->base_currency_symbol_position == 'left' ? $userBs->base_currency_symbol : '' }}
                                            {{ $total_fee }}
                                            {{ $userBs->base_currency_symbol_position == 'right' ? $userBs->base_currency_symbol : '' }}
                                        </span> <br>
                                        <span>
                                            <Strong>{{ $keywords['Payable_amount'] ?? 'Payable Amount' }} : </Strong>
                                            {{ $userBs->base_currency_symbol_position == 'left' ? $userBs->base_currency_symbol : '' }}
                                            {{ $price }}
                                            {{ $userBs->base_currency_symbol_position == 'right' ? $userBs->base_currency_symbol : '' }}
                                            @if (empty($userBs->full_payment))
                                                ({{ $userBs->advance_percentage }} %
                                                {{ $keywords['Advance'] ?? __('Advance') }})
                                            @endif
                                        </span>
                                        <input type="hidden" value="{{ $total_fee }}" name="total_price">
                                        <input type="hidden" value="{{ $price }}" name="price">
                                        <div class="select-wrapper">
                                            <select required name="payment_method" id="payment-gateway"
                                                class="olima_select form_control anyClass  mt-3 selected-payment-gateway">
                                                <option value="" selected disabled>
                                                    {{ $keywords['Choose_an_option'] ?? __('Choose an option') }}
                                                </option>
                                                @foreach ($payment_methods as $payment_method)
                                                    <option value="{{ $payment_method->name }}"
                                                        {{ old('payment_method') == $payment_method->name ? 'selected' : '' }}>
                                                        {{ $payment_method->name }}
                                                    </option>
                                                @endforeach
                                                @foreach ($offline as $payment_method)
                                                    <option value="{{ $payment_method->name }}"
                                                        {{ old('payment_method') == $payment_method->name ? 'selected' : '' }}>
                                                        {{ $payment_method->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('payment_method'))
                                            <span class="method-error">
                                                <strong>{{ $errors->first('payment_method') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- START: Stripe Card Details Form --}}
                            <div id="stripe-element" style="margin-top: 10px;" class="mb-2 ">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <!-- Used to display form errors -->
                            <div id="stripe-errors" class="pb-2 " role="alert"></div>
                            {{-- END: Stripe Card Details Form --}}

                            {{-- START: Authorize.net Card Details Form --}}
                            <div class="row gateway-details py-3" id="tab-anet" style="display: none;">
                                <div class="col-lg-6">
                                    <div class="form_group mb-3">
                                        <input class="form-control" type="text" id="anetCardNumber"
                                            placeholder="Card Number" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="form_group">
                                        <input class="form-control" type="text" id="anetExpMonth"
                                            placeholder="Expire Month" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 ">
                                    <div class="form_group">
                                        <input class="form-control" type="text" id="anetExpYear"
                                            placeholder="Expire Year" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 ">
                                    <div class="form_group">
                                        <input class="form-control" type="text" id="anetCardCode" placeholder="Card Code"
                                            disabled />
                                    </div>
                                </div>
                                <input type="hidden" name="opaqueDataValue" id="opaqueDataValue" disabled />
                                <input type="hidden" name="opaqueDataDescriptor" id="opaqueDataDescriptor" disabled />
                                <ul id="anetErrors"></ul>
                            </div>
                            {{-- END: Authorize.net Card Details Form --}}

                            <div>
                                <div id="instructions"></div>
                                <div class="form-element mb-2 payment_rec d-none">
                                    <label>{{ $keywords['Receipt'] ?? __('Receipt') }}<span>*</span></label><br>
                                    <input type="file" name="receipt" value="1" class="file-input">
                                    <p class="mb-0 text-warning">**
                                        {{ $keywords['Receipt_image_must_be'] ?? __('Receipt image must be') }} .jpg /
                                        .jpeg / .png</p>
                                </div>
                                <input type="hidden" name="is_receipt" value="0" id="is_receipt">
                                @if ($errors->has('receipt'))
                                    <span class="error">
                                        <strong>{{ $errors->first('receipt') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="text-center">
                                <button
                                    class="mt-30 w-100 @if ($userBs->theme == 1 || $userBs->theme == 2) template-btn @else main-btn @endif"
                                    type="submit">{{ $keywords['Confirm'] ?? __('Confirm') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!--====== PROFILE PART ENDS ======-->
@endsection
@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        "use strict";
        let instructionText = "{{ $keywords['Instructions'] ?? __('Instructions') }}"
        let shortDesText = "{{ $keywords['Short_Description'] ?? __('Short Description') }}"
        $("#payment-gateway").on('change', function() {
            let offline = @php echo json_encode($offline) @endphp;
            let data = [];
            offline.map(({
                id,
                name
            }) => {
                data.push(name);
            });
            let paymentMethod = $("#payment-gateway").val();
            $("input[name='payment_method']").val(paymentMethod);

            $(".gateway-details").hide();
            $(".gateway-details input").attr('disabled', true);


            if (paymentMethod == 'Stripe') {
                $('#stripe-element').removeClass('d-none');
            } else {
                $('#stripe-element').addClass('d-none');
            }
            if (paymentMethod == 'Authorize.net') {
                $("#tab-anet").show();
                $("#tab-anet input").removeAttr('disabled');
            }

            if (data.indexOf(paymentMethod) != -1) {
                let formData = new FormData();
                formData.append('name', paymentMethod);
                $.ajax({
                    url: '{{ route('front.payment.instructions') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    cache: false,
                    data: formData,
                    success: function(data) {
                        function nl2br(str) {
                            return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
                        }

                        function nl2bri(str) {
                            return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
                        }

                        let instruction = $("#instructions");
                        let instructions =
                            `<div class="gateway-desc"><strong>${instructionText} : </strong>  ${nl2bri(data.instructions)}</div>`;
                        if (data.description != null) {
                            var description =
                                `<div class="gateway-desc"><strong>${shortDesText} : </strong>
                                    <p> ${nl2br(data.description)}</p></div>`;
                        } else {
                            var description = `<div></div>`;
                        }

                        if (data.is_receipt == 1) {
                            $('.payment_rec').removeClass('d-none')
                            $("#is_receipt").val(1);
                            $("#is_receipt").attr('required', true);
                            let finalInstruction = instructions + description;
                            instruction.html(finalInstruction);
                        } else {
                            $("#is_receipt").val(0);
                            $("#is_receipt").attr('required', false);
                            $('.payment_rec').addClass('d-none')
                            let finalInstruction = instructions + description;
                            instruction.html(finalInstruction);
                        }
                        $('#instructions').fadeIn();
                    },
                    error: function(data) {}
                })
            } else {
                $('#instructions').fadeOut();
            }
        });

        // $(document).ready(function() { });
    </script>


    @php
        $anet = \App\Models\User\UserPaymentGateway::where('user_id', getUser()->id)
            ->where('keyword', 'authorize.net')
            ->first();
        $anerInfo = $anet->convertAutoData();
        $anetTest = $anerInfo['sandbox_check'] ?? '';

        if ($anetTest == 1) {
            $anetSrc = 'https://jstest.authorize.net/v1/Accept.js';
        } else {
            $anetSrc = 'https://js.authorize.net/v1/Accept.js';
        }
    @endphp

    @php
        $stripe_key = \App\Models\User\UserPaymentGateway::where('user_id', getUser()->id)
            ->where('keyword', 'stripe')
            ->first()->information;
        $stripe_key = json_decode($stripe_key);
        $stripe_key = $stripe_key->key;
    @endphp

    <script>
        let stripe_key = "{{ $stripe_key }}";
        let anit_public_key = "{{ $anerInfo['public_key'] }}";
        let login_id = "{{ $anerInfo['login_id'] }}";
    </script>
    <script type="text/javascript" src="{{ $anetSrc }}" charset="utf-8"></script>

    <script src="{{ asset('assets/front/js/stripe.js') }}"></script>
    {{-- END: Authorize.net Scripts --}}


    <script>
        @if (old('payment_method') == 'Stripe')
            $(document).ready(function() {
                $('#stripe-element').removeClass('d-none');
            })
        @endif
    </script>
@endsection
