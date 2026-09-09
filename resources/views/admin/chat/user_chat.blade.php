@extends('admin.layout.app')

@section('content')
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>User Id</th>
                <th>Message</th>
                <th>Reply</th>
            </tr>
        </thead>
        <tbody id="chat_table_body">
            <tr>
                <!-- rows populated dynamically -->
            </tr>
        </tbody>
    </table>

    {{-- Unified View + Reply Modal --}}
    <div class="modal fade" id="chatModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">       
                <!-- Header -->
                <div class="modal-header bg-success text-white">
                    <div class="d-flex flex-col gap-2">
                        <h5 class="modal-title">ShopCart Chat</h5>
                        <div id="status-indicator" class="mb-2"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <!-- Body -->
                <div class="modal-body chat-body d-flex flex-column h-100">
                    <!-- Chat history -->
                    <div 
                        id="chat-box-admin"
                        class="border rounded p-3 mb-3 flex-grow-1 overflow-auto"
                    >
                        <!-- messages will be injected here -->
                    </div>

                    <div class="input-group">
                        <label for="admin-chat-media" class="btn btn-light border rounded-start mb-0">
                            <i class="bi bi-paperclip"></i>
                        </label>
                        <input type="file" id="admin-chat-media" class="d-none" multiple>
                        <input type="text" id="admin-reply-message" class="form-control" placeholder="Enter reply">
                        <button id="admin-record-btn" class="btn btn-light">
                            <i class="bi bi-mic"></i>
                        </button>
                        <button id="admin-location-btn" class="btn btn-light">
                            <i class="bi bi-geo-alt"></i>
                        </button>
                        <button id="send-admin-reply" class="btn btn-success">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    <div id="admin-media-preview" class="mt-2"></div>
                    <audio id="admin-audio-preview" controls class="mt-2 d-none"></audio>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="adminLocationOptionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Share Location</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <button id="current-location-btn" class="btn btn-success w-100 mb-2">
                        Send Current Location
                    </button>
                    <button id="custom-location-btn" class="btn btn-outline-success w-100">
                        Enter Specific Location
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementById('admin-chat-media').addEventListener('change', function() {
            const files = this.files;
            const preview = document.getElementById('admin-media-preview');
            preview.innerHTML = ''; // clear old previews

            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail', 'me-2');
                        img.style.height = '50px';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-secondary', 'me-2');
                    badge.textContent = file.name;
                    preview.appendChild(badge);
                }
            });
        });

        let adminMediaRecorder;
        let adminAudioBlob = null;

        const adminRecordBtn = document.getElementById('admin-record-btn');
        const adminAudioPreview = document.getElementById('admin-audio-preview');

        adminRecordBtn.addEventListener('click', async () => {
            if (!adminMediaRecorder || adminMediaRecorder.state === "inactive") {
                const audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                adminMediaRecorder = new MediaRecorder(audioStream);
                const chunks = [];

                adminMediaRecorder.ondataavailable = e => chunks.push(e.data);
                adminMediaRecorder.onstop = () => {
                    adminAudioBlob = new Blob(chunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(adminAudioBlob);
                    adminAudioPreview.src = audioUrl;
                    adminAudioPreview.classList.remove('d-none');
                    audioStream.getTracks().forEach(track => track.stop());
                    audioStream = null;
                };

                adminMediaRecorder.start();
                adminRecordBtn.innerHTML = '<i class="bi bi-stop-fill"></i>';
                adminRecordBtn.classList.add('btn-danger');
            } else {
                adminMediaRecorder.stop();
                adminRecordBtn.innerHTML = '<i class="bi bi-mic"></i>';
                adminRecordBtn.classList.remove('btn-danger');
            }
        });
    </script>
@endsection