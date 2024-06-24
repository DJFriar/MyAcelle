@extends('layouts.core.backend')

@section('title', trans('postal::messages.title'))

@section('page_header')

	<div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li class="breadcrumb-item"><a href="http://localhost/admin">Home</a></li>
            <li class="breadcrumb-item"><a href="http://localhost/admin/sending_servers">Verification</a></li>
        </ul>
        <h1 class="mc-h1">
            <span class="text-semibold">Verify Email Address</span>
        </h1>
    </div>

@endsection

@section('content')

<form action="{{ action('Admin\VerificationController@index') }}" method="POST" class="form-validate-jqueryz">
    {{ csrf_field() }}

    <div class="mc_section">
        <div class="row">
            <div class="col-md-6">
                @if (isset($results) && $results['success'] == true)
                    <div class="alert alert-success" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                        <div style="display: flex; flex-direction: row; align-items: center;">
                            <div style="margin-right:15px">
                                <i class="lnr lnr-checkmark-circle"></i>
                            </div>
                            <div style="padding-right: 40px">
                                <h4>Valid</h4>
                                <p>Email provided email address "{{ $email }}" is valid. Status: DELIVERABLE. Below are the MX servers found:
                                </p>
                                <p>
                                    {!! $mxs !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif (isset($results))
                    <div class="alert alert-danger" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                        <div style="display: flex; flex-direction: row; align-items: center;">
                            <div style="margin-right:15px">
                                <i class="lnr lnr-circle-minus"></i>
                            </div>
                            <div style="padding-right: 40px">
                                <h4>Invalid</h4>
                                <p>The email given address "{{ $email }}" is invalid or does not exist in the remote server</p>
                            </div>
                        </div>
                    </div>
                @endif
                <p>Enter your email address to verify. Acelle will MX records for the email domain and then connects to the domain's SMTP server to try figuring out if the address really exists.</p>
                <div class="form-group control-password">
                    <label>Email Address<span class="text-danger">*</span></label>

                    <div>
                        <input type="text" id="email" value="" autocomplete="new-password" name="email" class="form-control required has-eye">
                    </div>
                </div>
            </div>
        </div>
        <div class="text-left">
            <span class="cancel-group">
                <button class="btn btn-mc_primary mr-10">Verify Email Address</button>
            </span>
        </div>
    </div>
</form>

@endsection

