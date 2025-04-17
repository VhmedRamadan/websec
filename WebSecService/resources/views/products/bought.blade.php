@extends('layouts.master')

@section('content')
<div class="container">
    <h2>bought products</h2>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">user id</th>
                    <th scope="col">product id</th>
                    <th scope="col">bought at</th>
                    </tr>
            </thead>
            <tbody>
                @foreach($boughtProducts as $purchase)
                <tr>
                    <td scope="col">{{$purchase->uid}}</td>
                    <td scope="col">{{$purchase->pid}}</td>
                    <td scope="col">{{$purchase->created_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
</div>
@endsection
