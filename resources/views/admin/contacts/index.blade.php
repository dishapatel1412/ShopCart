@extends('admin.layout.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5>📩 Contact Messages</h5>
        </div>
    
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Message</th>
                    </tr>
                </thead>
            
                <tbody>
                    @foreach($contacts as $contact)
                    <tr>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->mobile_num }}</td>
                        <td>{{ $contact->message }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection