@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Customers List</h2>

    @if($customers->isEmpty())
        <div class="alert alert-warning">No customers found.</div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Roles</th>
                    <th scope="col">Credit</th>
                    <th scope="col"></th>
                    </tr>
            </thead>
            <tbody>
                @foreach($customers as $user)
                <tr>
                    <td scope="col">{{$user->id}}</td>
                    <td scope="col">{{$user->name}}</td>
                    <td scope="col">{{$user->email}}</td>
                    <td scope="col">
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{$role->name}}</span>
                        @endforeach
                    </td>
                    <td scope="col">{{$user->credit}}</td>
                    <td scope="col">
                        @can('edit_users')
                        <a class="btn btn-primary" href='{{route('users_edit', [$user->id])}}'>Edit</a>
                        @endcan
                        @can('admin_users')
                        <a class="btn btn-primary" href='{{route('edit_password', [$user->id])}}'>Change Password</a>
                        @endcan
                        @can('delete_users')
                        <a class="btn btn-danger" href='{{route('users_delete', [$user->id])}}'>Delete</a>
                        @endcan
                        <a class="btn btn-primary" href='{{route('bought', [$user->id])}}'>show purchases</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
