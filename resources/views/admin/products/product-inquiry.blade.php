@extends('admin.layout.app')

@section('content')
    <h3>Product Inquiries</h3>

    <table class="table">
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
                        <button type="button"
                                class="btn btn-info fw-semibold open-reply-modal"
                                style="border:1px solid #6c757d; border-radius:12px;"
                                data-id="{{ $inq->id }}"
                                data-bs-toggle="modal"
                                data-bs-target="#replyModal">
                            Reply
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="modal fade" id="replyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <form id="replyForm" action="{{ route('admin.inquiry.reply', $inq->id) }}" method="POST">
                        @csrf           
                        <div class="mb-2">
                            <textarea name="reply_message"
                                    class="form-control"
                                    placeholder="Write reply..."
                                    required>
                            </textarea>
                        </div>
                        <button class="btn btn-dark btn-sm">
                            Reply
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.open-reply-modal').forEach(button => {
            button.addEventListener('click', function () {

                const inquiryId = this.getAttribute('data-id');

                const form = document.getElementById('replyForm');

                form.action = `/admin/inquiry/${inquiryId}/reply`;
            });
        });
    </script>
@endsection
