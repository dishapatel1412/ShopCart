@extends('layouts.panel')

@section('panelContent')
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-4">Your Inquiries</h4>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Reply</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inquiries as $inq)
                                    <tr>
                                        <td>{{ $inq->user->name }}</td>
                                        <td>{{ $inq->product->name }}</td>
                                        <td>{{ $inq->message }}</td>
                                        <td>{{ $inq->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($inq->reply_message)
                                                <div class="mt-2 p-2 bg-light border rounded">
                                                    <p class="mb-0">{{ $inq->reply_message }}</p>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection