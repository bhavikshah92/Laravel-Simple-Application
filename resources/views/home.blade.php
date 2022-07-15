@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <!--<h3>Welcome {{Auth::user()->name}}</h3>-->
                    <h3>Welcome {{session('name')}}</h3>
                    <small>{{session('usertype')}}</small>
                </div>
                <div class="card-footer">
                    You are logged in using {{session('email')}}!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection