@extends('layouts.app')
@section('title', "Company Profile")
@section('content') 

<div class="content">
    @if($errors->any())
    <div class="alert alert-danger">
        <div><b>Waiitt! You got an error massages <i class="em em-confounded"></i></b></div>
        @foreach ($errors->all() as $error)
        <div>> {{ $error }}</div>
        @endforeach
    </div>
    @endif
    <div class="block block-fx-shadow">
        <div class="block-content">
            @foreach($company as $company)
            <form action="{{action('CompanyController@update',  $company->id ) }}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
				{{ method_field('PUT') }}
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
                                <input type="text" class="form-control form-control-lg" value="{{$company->username}}" name="username">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row items-push">
                    <div class="col-lg-3">
                        <p class="text-muted">
                        <img width="150px" height="150px" src="{{ asset('uploads/documents/'.$company->logo) }}" class="rounded-circle ml-30c">
                        </p>
                    </div>
                    <div class="col-lg-7 offset-lg-1">
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Upload your Company logo:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="logo" data-toggle="custom-file-input" accept=".jpg, .png, .jpeg, .bmp">
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
                                <input type="text" class="form-control form-control-lg" value="{{$company->name}}" name="name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Email</label>
                                <input type="email" class="form-control form-control-lg" value="{{$company->email}}" name="email" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Phone</label>
                                <input type="number" class="form-control form-control-lg" value="{{$company->phone}}" name="phone" required>
                            </div>
                            <div class="col-6">
                                <label>FAX</label>
                                <input type="number" class="form-control form-control-lg" value="{{$company->fax}}" name="fax" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label>Street Address</label>
                                <textarea class="form-control form-control-lg" name="address" value="{{$company->address}}" required></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Province</label>
                                <select class="form-control form-control-lg" id="province" name="province" required>
                                    <option disabled selected>Choose your Province</option>
                                    @foreach($province as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label>City</label>
                                <select class="form-control form-control-lg" id="city" name="city" required>
                                    <option disabled selected>Choose your City</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Postal code</label>
                                <input type="text" class="form-control form-control-lg" value="{{$company->postal_code}}" name="postal_code" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-block btn-alt-primary"><i class="fa fa-save mr-2"></i>Save</button>
                    </div>
                </div>
            </form>
            @endforeach
        </div>
    </div>
</div>
@endsection

