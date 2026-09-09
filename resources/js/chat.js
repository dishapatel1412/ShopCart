import { db } from './firebase';
import {
    doc,
    setDoc,
    addDoc,
    getDoc,
    updateDoc,
    collection,
    arrayUnion,
    onSnapshot,
    Timestamp,
    query,
    orderBy
} from 'firebase/firestore';

let activeChatType = null;
let activeChatId = null;
let mediaRecorder = null;
let audioBlob = null;
let adminUnsubscribe = null;
let groupUnsubscribe = null;

document.addEventListener('DOMContentLoaded', () => {
    const sendBtn = document.getElementById('send-btn');
    const recordBtn = document.getElementById('record-btn');
    const locationBtn = document.getElementById('location-btn');
    const audioPreview = document.getElementById('audio-preview');
    const message_input = document.getElementById('message');
    const media_input = document.getElementById('chat-media');
    const chat_box = document.getElementById('chat-box');

    const locationModal = new bootstrap.Modal(document.getElementById('locationOptionsModal'));
    const createGroupModal = new bootstrap.Modal(document.getElementById('createGroupModal'));
    
    const adminDocRef = doc(db, 'messages', `user_${window.userId}`);
    // const docRef = doc(db, 'messages', `user_${window.userId}`);
    // console.log("window.userId =", window.userId);
    // console.log("docRef path =", docRef.path);
    function setActiveConversation(element) {
        document.querySelectorAll('.conversation-row').forEach(row => {
            row.classList.remove('active-chat');
        });
        element.classList.add('active-chat');
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', async () => {
            const message = message_input.value;
            const file = media_input.files[0];
            let mediaUrl = '';
            if (message.trim() === "" && !file && !audioBlob) return;

            try {
                if (file || audioBlob) {
                    const formData = new FormData();
                    formData.append('chat-media', file || audioBlob, file ? file.name : 'voice-message.webm');

                    const response = await fetch('/chat/upload', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (!response.ok) throw new Error('Upload Failed');
                    const data = await response.json();
                    mediaUrl = data.url;
                }

                if(!activeChatType) {
                    alert('Please start a conversation.');
                    return;
                }

                if (activeChatType === 'admin') {
                    await setDoc(adminDocRef, {user_id: window.userId}, {merge:true});
                    await updateDoc(adminDocRef, {
                        chat: arrayUnion({
                            sender: 'user',
                            text: message,
                            media_url: mediaUrl,
                            created_at: Timestamp.now(),
                            status: 'sent'
                        })
                    });
                } else if (activeChatType === 'group') {
                    const groupDocRef = doc(db, 'groups', `group_${activeChatId}`);
                    await setDoc(
                        groupDocRef,
                        {
                            chat: arrayUnion({
                                sender: 'user',
                                sender_id: window.userId,
                                sender_name: window.userName,
                                text: message,
                                media_url: mediaUrl,
                                created_at: Timestamp.now(),
                                status: 'sent'
                            })
                        },
                        {
                            merge: true
                        }
                    );
                }
                message_input.value = '';
                media_input.value = '';
                audioBlob = null;
                audioPreview.src = '';
                audioPreview.classList.add('d-none');
            } catch (error) {
                console.log('Firestore Error: ', error);
            }
        });

        recordBtn.addEventListener('click', async () => {
            if (!mediaRecorder || mediaRecorder.state === "inactive") {
                const audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(audioStream);
                const chunks = [];

                mediaRecorder.ondataavailable = e => chunks.push(e.data);
                mediaRecorder.onstop = () => {
                    audioBlob = new Blob(chunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPreview.src = audioUrl;
                    audioPreview.classList.remove('d-none');
                    audioStream.getTracks().forEach(track => track.stop());
                    audioStream = null;
                };
                audioPreview.src = '';

                mediaRecorder.start();
                recordBtn.innerHTML = '<i class="bi bi-stop-fill"></i>';
                recordBtn.classList.toggle('btn-danger', mediaRecorder.state === 'recording');
            } else {
                mediaRecorder.stop();
                recordBtn.innerHTML = '<i class="bi bi-mic"></i>';
            }
        });

        async function sendLocation(latitude, longitude) {
            if (!activeChatType) {
                alert('Please select a conversation first.');
                return;
            }

            const locationMessage = {
                sender: 'user',
                sender_id: window.userId,
                sender_name: window.userName,
                type: 'location',
                latitude,
                longitude,
                created_at: Timestamp.now(),
                status: 'sent'
            };

            if (activeChatType === 'admin') {
                await setDoc(
                    adminDocRef,
                    {
                        user_id: window.userId
                    },
                    {
                        merge: true
                    }
                );
                await updateDoc(adminDocRef, {
                    chat: arrayUnion(locationMessage)
                });
            } else if (activeChatType === 'group') {
                const groupDocRef = doc(db, 'groups', `group_${activeChatId}`);
                await updateDoc(groupDocRef,{
                    chat: arrayUnion(locationMessage)
                });
            }

            await fetch('/api/chat/location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({latitude, longitude})
            });
        }

        locationBtn.addEventListener('click', async () => {
            locationModal.show();
        });

        // Current location
        document.getElementById('current-location-btn').addEventListener('click', () => {
            navigator.geolocation.getCurrentPosition(async (position) => {
                await sendLocation(position.coords.latitude, position.coords.longitude);
                locationModal.hide();
            },
            (error) => {
                console.error(error);
            });
        });

        // Custom location
        document.getElementById('custom-location-btn').addEventListener('click', async () => {
            const lat = prompt("Enter latitude:");
            const lng = prompt("Enter longitude:");

            if (!lat || !lng) return;
            await sendLocation(parseFloat(lat), parseFloat(lng));
            locationModal.hide();
        });
    }

    async function markAdminMessagesDelivered(adminDocRef, chat) {
        const hasUndelivered = chat.some(msg => msg.sender === 'admin' && msg.status === 'sent');
        if (!hasUndelivered) return;

        const updated = chat.map(msg => {
            if (msg.sender === 'admin' && msg.status === 'sent') {
                return { ...msg, status: 'delivered' };
            }
            return msg;
        });

        try {
            await updateDoc(adminDocRef, { chat: updated });
        } catch (err) {
            console.error('Error updating delivered status:', err);
        }
    }

    async function markAdminMessagesSeen(adminDocRef, chat) {
        const hasUnseen = chat.some(msg => msg.sender === 'admin' && msg.status === 'delivered');
        if (!hasUnseen) return;

        const updated = chat.map(msg => {
            if (msg.sender === 'admin' && msg.status === 'delivered') {
                return { ...msg, status: 'seen' };
            }
            return msg;
        });

        try {
            await updateDoc(adminDocRef, { chat: updated });
        } catch (err) {
            console.error('Error updating seen status:', err);
        }
    }
    
    document.addEventListener('click', function(e){
        const row = e.target.closest('.conversation-row');

        if(!row) return;

        activeChatType = row.dataset.type;
        activeChatId = row.dataset.id;

        const chatTitle = document.getElementById('chat-title');
        if (activeChatType === 'admin') {
            chatTitle.textContent = 'ShopCart Admin';
        } else {
            chatTitle.textContent = row.textContent.trim();
        }

        document.querySelectorAll('.conversation-row').forEach(item => item.classList.remove('active'));
        row.classList.add('active');
        loadConversation(activeChatType, activeChatId);
    });

    function appendMessage(message)
    {
        const isMyMessage = activeChatType === 'group' ? message.sender_id == window.userId : message.sender === 'user';
        let tickIcon = '';
        if (isMyMessage) {
            if (message.status === 'seen') {
                tickIcon = '<i class="bi bi-check-all text-primary"></i>';
            }
            else if (message.status === 'delivered') {
                tickIcon = '<i class="bi bi-check-all text-muted"></i>';
            }
            else {
                tickIcon = '<i class="bi bi-check text-muted"></i>';
            }
        }

        if (message.type === 'location') {
            chat_box.innerHTML += `
                <div class="d-flex ${isMyMessage ? 'justify-content-start' : 'justify-content-end'} mb-2">
                    <div class="location-card shadow-sm">
                        <iframe
                            src="https://maps.google.com/maps?q=${message.latitude},${message.longitude}&z=16&output=embed"
                            width="300"
                            height="180"
                            style="border:none;border-radius:12px 12px 0 0;">
                        </iframe>
                        <div class="d-flex flex-column p-2 bg-white">
                            <strong>
                                ${message.location_name || 'Shared Location'}
                            </strong>
                            <a
                                href="https://www.google.com/maps?q=${message.latitude},${message.longitude}"
                                target="_blank"
                                class="small text-primary text-decoration-none"
                            >
                                Open in Google Maps
                            </a>
                            <div class="text-end small mt-1">
                                ${tickIcon}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        chat_box.innerHTML += `
            <div
                style="margin-top:10px"
                class="d-flex ${
                    isMyMessage ? 'justify-content-end' : 'justify-content-start'
                }"
            >
                <div
                    style="max-width:70%;"
                    class="chat-bubble ${
                        isMyMessage ? 'user-bubble' : 'admin-bubble'
                    }"
                >
                    ${
                        activeChatType === 'group' && !isMyMessage
                        ? `
                            <div class="fw-bold small text-success mb-1">
                                ${message.sender_name ?? 'Unknown'}
                            </div>
                        `
                        : ''
                    }
                    <div>
                        ${message.text || message.message || ''}
                    </div>

                    ${renderMedia(message.media_url)}
                    <div class="text-end small mt-1">
                        ${tickIcon}
                    </div>
                </div>
            </div>
        `;
    }

    function renderMedia(mediaUrl)
    {
        if (!mediaUrl) return '';
        if (/\.(jpg|jpeg|png|gif)(\?|$)/i.test(mediaUrl)) {
            return `
                <img
                    src="${mediaUrl}"
                    width="150"
                    class="rounded mt-2 d-block"
                >
            `;
        }

        if (/\.(mp4|mov|avi|mkv)(\?|$)/i.test(mediaUrl)) {
            return `
                <video controls width="250" class="mt-2 d-block rounded">
                    <source src="${mediaUrl}">
                </video>
            `;
        }

        if (/\.(mp3|wav|webm)(\?|$)/i.test(mediaUrl)) {
            return `
                <audio controls class="mt-2 d-block">
                    <source src="${mediaUrl}">
                </audio>
            `;
        }

        if (/\.pdf(\?|$)/i.test(mediaUrl)) {
            return `
                <div class="pdf-card mt-2 border rounded p-2 bg-white">
                    <a
                        href="${mediaUrl}"
                        target="_blank"
                        class="text-decoration-none text-dark"
                    >
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                            <div>
                                <div class="fw-semibold">
                                    PDF Document
                                </div>
                                <small class="text-muted">
                                    Click to open PDF
                                </small>
                            </div>
                        </div>
                    </a>
                    <a
                        href="${mediaUrl}"
                        download
                        class="btn btn-outline-success mt-2"
                    >
                        Download
                    </a>
                </div>
            `;
        }
        return `
            <a
                href="${mediaUrl}"
                target="_blank"
                class="d-block mt-2"
            >
                Download File
            </a>
        `;
    }

    function updateAdminStatus(data)
    {
        const statusElement = document.getElementById('status-indicator');
        if (!statusElement) return;
        if (data?.admin_status === 'online') {
            statusElement.innerHTML = `
                <span class="badge bg-success">
                    Online
                </span>
            `;
        }
        else {
            const lastSeen = data?.admin_last_seen
                ? data.admin_last_seen.toDate().toLocaleString()
                : 'never';

            statusElement.innerHTML = `
                <span class="badge bg-secondary">
                    Offline
                </span>
                <small class="text-muted">
                    (last seen ${lastSeen})
                </small>
            `;        
        }
    }

    function loadConversation(type, id)
    {
        chat_box.innerHTML = '';

        if(adminUnsubscribe) {
            adminUnsubscribe();
            adminUnsubscribe = null;
        }

        if(groupUnsubscribe)
        {
            groupUnsubscribe();
            groupUnsubscribe = null;
        }

        if (type === "admin") {
            loadAdminChat();
        }

        if (type === "group") {
            document.getElementById('status-indicator').innerHTML = '';
            loadGroupChat(id);
        }
    }

    function loadAdminChat()
    {
        adminUnsubscribe = onSnapshot(adminDocRef, (snapshot) => {
            chat_box.innerHTML = '';

            if(!snapshot.exists()) return;

            const data = snapshot.data();
            updateAdminStatus(data);

            if(!data.chat) return;

            const sorted = [...data.chat].sort((a, b) => {
                const aTime = a.created_at?.seconds ?? a.created_at;
                const bTime = b.created_at?.seconds ?? b.created_at;
                return aTime - bTime;
            });

            sorted.forEach(message => {
                appendMessage(message);
            });

            chat_box.scrollTop = chat_box.scrollHeight;
            markAdminMessagesDelivered(adminDocRef, data.chat);

            if (
                document.getElementById('chatModal') ?.classList.contains('show')
            ) {
                markAdminMessagesSeen(adminDocRef,data.chat);
            }
        });
    }

    function loadGroupChat(groupId)
    {
        const groupDocRef = doc(db, 'groups', `group_${groupId}`);

        groupUnsubscribe = onSnapshot(groupDocRef, (snapshot) => {
            chat_box.innerHTML = '';

            if(!snapshot.exists()) return;

            const data = snapshot.data();

            if(!data.chat) return;

            const sorted = [...data.chat].sort((a, b) => {
                const aTime = a.created_at?.seconds ?? a.created_at;
                const bTime = b.created_at?.seconds ?? b.created_at;

                return aTime - bTime;
            });

            sorted.forEach(message => {
                appendMessage(message);
            });

            chat_box.scrollTop = chat_box.scrollHeight;
        });
    }

    document.getElementById('chatModal')?.addEventListener('shown.bs.modal', async () => {
        const snapshot = await getDoc(adminDocRef);
        if (snapshot.exists()) {
            const data = snapshot.data();
            if (data.chat) {
                await markAdminMessagesSeen(adminDocRef, data.chat);
            }
        }

        await updateDoc(adminDocRef, {
            user_status: 'online',
            user_last_seen: Timestamp.now()
        });
    });

    document.getElementById('chatModal')?.addEventListener('hidden.bs.modal', async () => {
        await updateDoc(adminDocRef, {
            user_status: 'offline',
            user_last_seen: Timestamp.now()
        });
    });

    document.addEventListener('visibilitychange', async () => {
        if (document.visibilityState === 'hidden' && document.getElementById('chatModal')?.classList.contains('show')) {
            return;
        }
        if (document.visibilityState === 'hidden') {
            await updateDoc(adminDocRef, { user_status: 'offline', user_last_seen: Timestamp.now() });
        } else if (document.getElementById('chatModal')?.classList.contains('show')) {
            await updateDoc(adminDocRef, { user_status: 'online', user_last_seen: Timestamp.now() });
        }
    });

    media_input.addEventListener('change', function () {
        const file = this.files[0];
        const previewContainer = document.getElementById('media-preview');
        previewContainer.innerHTML = '';
        if (!file) return;
        // Image preview
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.classList.add('img-thumbnail', 'mt-2');
            img.style.maxWidth = '200px';

            previewContainer.appendChild(img);
        }

        // Video preview
        else if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.controls = true;
            video.classList.add('mt-2');
            video.style.maxWidth = '250px';

            previewContainer.appendChild(video);
        }

        // Audio preview
        else if (file.type.startsWith('audio/')) {
            const audio = document.createElement('audio');
            audio.src = URL.createObjectURL(file);
            audio.controls = true;
            audio.classList.add('mt-2');

            previewContainer.appendChild(audio);
        }
    });
});