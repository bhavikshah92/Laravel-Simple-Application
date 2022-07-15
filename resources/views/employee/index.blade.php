@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Employee Details

                    <a style="float:right;" href="{{route('employee.create')}}">Add New</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif
                    <br />
                    <form method="POST" enctype="multipart/form-data" action="{{ route('import.content') }}">
                        @csrf
                        <input type="file" id="uploaded_file" name="uploaded_file" />
                        <button type="submit" class="btn btn-primary">
                            {{ __('Upload CSV') }}
                        </button>
                    </form>
                    <br />
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email Id</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no=1;
                            ?>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{$no++}}</td>
                                <td>{{$employee->name}}</td>
                                <td>{{$employee->emailid}}</td>
                                <td>{{$employee->address}}</td>
                                <td>{{$employee->phone}}</td>
                                <td><a href="{{ route('employee.edit', ['id'=>$employee->id])}}" class="btn btn-primary">Edit</a></td>
                                <td>
                                    <form action="{{ route('employee.destroy', ['id'=>$employee->id])}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </br>
                </div>
                <div class="card-footer">
                    <p>Welcome {{Auth::user()->name}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#example').DataTable();
});
</script>
@endsection