@extends('layouts.welcome')
@section('title', "Welcome")
@section('content')
<div class="content">
    <div class="text-center" style="margin-bottom: 120px">
        <h1><i class="em em-rocket rocket"></i></h1>
    </div>
    <div class="text-center" style="margin-top: 100px; margin-bottom: 50px;">
        <h3 class="h5 text-muted mb-10">Almost ready to flight.</h3>
        <h2 class="font-w700 text-black mb-10 hello">Hello, {{ Auth::user()->name }}</h2>
        <h3 class="h5 mb-0">Please fill your company detail before.</h3>
    </div>
    
    @if($errors->any())
    <div class="alert alert-danger">
        <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
        @foreach ($errors->all() as $error)
        <div> {{ $error }}</div>
        @endforeach
    </div>
    @endif
    
    <div class="block block-fx-shadow">
        <div class="block-content">
            <form action="{{ route('welcome_create') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <h2 class="content-heading text-black pt-0">Account Information</h2>
                <div class="row items-push">
                    <div class="col-lg-3">
                        <p class="text-muted">
                            The detail cannot be changed. Make sure you fill the data correctly.
                        </p>
                    </div>
                    <div class="col-lg-7 offset-lg-1">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Username</label>
                                <input type="text" class="form-control form-control-lg" name="username">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row items-push">
                    <div class="col-lg-3">
                        <p class="text-muted">
                            Please upload your Company logo.
                        </p>
                    </div>
                    <div class="col-lg-7 offset-lg-1">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Upload your Company logo:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp" required>
                                    <label class="custom-file-label">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="content-heading text-black">Company Details</h2>
                <div class="row items-push">
                    <div class="col-lg-3">
                        <p class="text-muted">
                            We need your company details for information.
                        </p>
                    </div>
                    <div class="col-lg-7 offset-lg-1">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Name</label>
                                <input type="text" class="form-control form-control-lg" name="name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Email</label>
                                <input type="email" class="form-control form-control-lg" name="email" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Phone</label>
                                <input type="number" class="form-control form-control-lg" name="phone" required>
                            </div>
                            <div class="col-6">
                                <label>FAX</label>
                                <input type="number" class="form-control form-control-lg" name="fax" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Street Address</label>
                                <textarea class="form-control form-control-lg" name="address" required></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Province</label>
                                <select class="js-select2 form-control form-control-lg" id="province" name="province" required>
                                    <option value="" disabled selected>Choose your Province</option>
                                    @foreach($province as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label>City</label>
                                <select class="js-select2 form-control form-control-lg" id="city" name="city" required>
                                    <option value="" disabled selected>Choose your City</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Postal code</label>
                                <input type="text" class="form-control form-control-lg" name="postal_code" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configuration --}}
                <h2 class="content-heading text-black pt-0">Configuration</h2>
                <div class="row items-push">
                    <div class="col-lg-3">
                        <p class="text-muted">
                            Choose your company pricing type.
                        </p>
                    </div>
                    <div class="col-lg-7 offset-lg-1">
                        <div class="form-group row">
                            <label class="col-12">Price Type</label>
                            <div class="col-12">
                                <select name="price" id="ptype" class="js-select2 form-control form-control-lg">
                                    <option value="" disabled selected>Choose your Price type</option>
                                    <option value="multi">Sell in, Sell Out</option>
                                    <option value="one">One Price</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row items-push" id="pricetype">
                    <div class="col-lg-3">
                        <p class="text-muted">
                            
                        </p>
                    </div>
                    <div class="col-lg-7 offset-lg-1">
                        <div class="form-group row">
                            <label class="col-12">Stock Type</label>
                            <div class="col-12">
                                <select name="stock" class="js-select2 form-control form-control-lg">
                                    <option value="" disabled selected>Choose your Price type</option>
                                    <option value="1">Sell In</option>
                                    <option value="2">Sell Out</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="row items-push">
                    <div class="col-12">
                        <button type="submit" class="btn btn-block btn-alt-primary"><i class="fa fa-save mr-2"></i>Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<style type="text/css">
@keyframes rocket {
    0% {
        margin-top: 0px;
    }
    50% {
        margin-top: 20px;
    }
    100% {
        margin-top: 0px;
    }
}
.rocket {
    position: absolute;
    margin-left: -20px;
    animation: rocket 0.1s infinite;
    animation-duration: 4s;
}
</style>
@endsection
@section('script')
<script type="text/javascript">
    $(".js-select2").select2();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#province').on('change', e => {
        var id = $('#province').find(":selected").val()
        $('#city').empty()
        $.ajax({
            type: "GET",
            url: "{{ route('getCity') }}?id="+id,
            success: data => {
                // console.log(data);
                data.forEach(city =>
                    $('#city').append(`<option value="${city.id}">${city.name}</option>`)
                    )
            }
        })
    })
    $('#ptype').on('change', e => {
        var val = $('#ptype').find(":selected").val()
        if (val == "multi") {
            $('#pricetype').show()
        } else {
            $('#pricetype').hide()
        }
    })
</script>
@endsection