
@extends('dashboard')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Employee Lists</h1>


        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Employees</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Employee ID</th>
                                <th>Email ID</th>
                                <th>Password</th>
                                <th>Department</th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $val )

                            <tr>
                                <td>{{$val->firstname}}</td>
                                <td>{{$val->employee_id}}</td>
                                <td>{{$val->email}}</td>
                                <td>{{$val->password}}</td>
                                <td>{{$val->users_department}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $data->links('pagination::bootstrap-5')}}
                </div>
            </div>
        </div>

    </div>
@endsection
