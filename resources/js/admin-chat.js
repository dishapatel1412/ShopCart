// import { db } from './firebase';
// import { 
//     collection,
//     getDocs,
//     doc,
//     updateDoc,
//     arrayUnion,
//     onSnapshot,
//     Timestamp,
//     setDoc,
//     getDoc
// } from 'firebase/firestore';

// let selectedUserId = null;
// const tableBody = document.getElementById('chat_table_body');
// const chat_box_admin = document.getElementById('chat-box-admin');
// const locationBtn = document.getElementById('admin-location-btn');

// // Load all user documents
// async function loadAllUsers() {
//     const snapshot = await getDocs(collection(db, 'messages'));
//     tableBody.innerHTML = '';

//     snapshot.forEach((docSnap) => {
//         const data = docSnap.data();
//         const lastMessage = data.chat?.[data.chat.length - 1];
//         const userId = data.user_id || docSnap.id.replace('user_', '');
//         if (!userId) return;

//         tableBody.innerHTML += `
//             <tr>
//                 <td>${userId}</td>
//                 <td>${lastMessage ? lastMessage.text : '' || lastMessage?.message || ''}</td>
//                 <td>
//                     <button class="btn btn-primary btn-sm reply-btn"
//                             data-user-id="${userId}"
//                             data-bs-toggle="modal"
//                             data-bs-target="#chatModal">
//                         Reply
//                     </button>
//                 </td>
//             </tr>
//         `;
//     });
// }

// async function markUserMessagesDelivered(docRef, chat) {
//     const hasUndelivered = chat.some(msg => msg.sender === 'user' && msg.status === 'sent');
//     if (!hasUndelivered) return;

//     const updated = chat.map(msg => {
//         if (msg.sender === 'user' && msg.status === 'sent') {
//             return { ...msg, status: 'delivered' };
//         }
//         return msg;
//     });
    
//     try {
//         await updateDoc(docRef, { chat: updated });
//     } catch (err) {
//         console.error('Error marking delivered:', err);
//     }
// }

// async function markUserMessagesSeen(docRef, chat) {
//     const hasUnseen = chat.some(msg => msg.sender === 'user' && msg.status === 'delivered');
//     if (!hasUnseen) return;

//     const updated = chat.map(msg => {
//         if (msg.sender === 'user' && msg.status === 'delivered') {
//             return { ...msg, status: 'seen' };
//         }
//         return msg;
//     });
    
//     try {
//         await updateDoc(docRef, { chat: updated });
//     } catch (err) {
//         console.error('Error marking seen:', err);
//     }
// }

// const adminLocationModal = new bootstrap.Modal(
//     document.getElementById('adminLocationOptionsModal')
// );

// locationBtn?.addEventListener('click', () => {
//     adminLocationModal.show();
// });

// // Handle modal open (view + reply)
// document.addEventListener('click', (event) => {
//     if (event.target.classList.contains('reply-btn')) {
//         selectedUserId = event.target.dataset.userId;
//         const docRef = doc(db, 'messages', `user_${selectedUserId}`);

//         onSnapshot(docRef, (snapshot) => {
//             chat_box_admin.innerHTML = '';
//             if (!snapshot.exists()) return;

//             const data = snapshot.data();
//             if (!data.chat) return;

//             const statusElement = document.getElementById('status-indicator');
//             if (data?.user_status === 'online') {
//                 statusElement.innerHTML = '<span class="badge bg-success">Online</span>';
//             } else {
//                 const lastSeen = data?.user_last_seen
//                     ? data.user_last_seen.toDate().toLocaleString()
//                     : 'never';
//                 statusElement.innerHTML = `<span class="badge bg-secondary">Offline</span> (last seen ${lastSeen})`;
//             }

//             const sorted = [...data.chat].sort((a, b) => {
//                 const aTime = a.created_at?.seconds || a.created_at;
//                 const bTime = b.created_at?.seconds || b.created_at;
//                 return aTime - bTime;
//             });

//             sorted.forEach((message) => {
//                 let tickIcon = '';
//                 if (message.sender === 'admin') {
//                     if (message.status === 'seen') {
//                         tickIcon = '<i class="bi bi-check-all text-primary"></i>';
//                     } else if (message.status === 'delivered') {
//                         tickIcon = '<i class="bi bi-check-all text-muted"></i>';
//                     } else {
//                         tickIcon = '<i class="bi bi-check text-muted"></i>';
//                     }
//                 }

//                 if (message.type === 'location') {
//                     chat_box_admin.innerHTML += `
//                         <div class="d-flex ${
//                             message.sender === 'admin'
//                                 ? 'justify-content-start'
//                                 : 'justify-content-end'
//                             }
//                         mb-2">
//                         <div class="location-card shadow-sm">
//                             <iframe
//                                 src="https://maps.google.com/maps?q=${message.latitude},${message.longitude}&z=16&output=embed"
//                                 width="300"
//                                 height="180"
//                                 style="border:none;border-radius:12px 12px 0 0;">
//                             </iframe>
//                             <div class="d-flex flex-col p-2 bg-white">
//                                 <strong>
//                                     ${
//                                         message.location_name || 'Shared Location'
//                                     }
//                                 </strong>
//                                 <a href="https://www.google.com/maps?q=${message.latitude},${message.longitude}"
//                                         target="_blank"
//                                         class="small text-primary text-decoration-none">
//                                     Open in Google Maps
//                                 </a>
//                                 <div class="text-end small mt-1">
//                                     ${tickIcon}
//                                 </div>
//                             </div>
//                         </div>
//                     </div>
//                     `;
//                     return;
//                 }

//                 chat_box_admin.innerHTML += `
//                     <div
//                         style="margin-top:10px"
//                         class="d-flex ${
//                             message.sender === 'admin'
//                                 ? 'text-body justify-content-start'
//                                 : 'text-body justify-content-end'
//                         }"
//                     >
//                         <div
//                             style="max-width:70%;"
//                             class="chat-bubble ${
//                                 message.sender === 'admin'
//                                     ? 'admin-bubble'
//                                     : 'user-bubble'
//                             }"
//                         >
//                             <div>${message.text || message.message || ''}</div>
//                             ${
//                                 message.media_url
//                                     ? message.media_url.match(/\.(jpg|jpeg|png|gif)(\?|$)/i)
//                                         ? `
//                                             <img
//                                                 src="${message.media_url}"
//                                                 width="150"
//                                                 class="rounded mt-2 d-block"
//                                             />
//                                         `
//                                         : message.media_url.match(/\.(mp4|mov|avi|mkv)(\?|$)/i)
//                                         ? `
//                                             <video controls width="250" class="mt-2 d-block rounded">
//                                                 <source src="${message.media_url}">
//                                                 Your browser does not support video playback.
//                                             </video>
//                                         `
//                                         : message.media_url.match(/\.(mp3|wav|webm)(\?|$)/i)
//                                         ? `
//                                             <audio controls class="mt-2 d-block">
//                                                 <source src="${message.media_url}">
//                                             </audio>
//                                         `
//                                         : message.media_url.match(/\.pdf(\?|$)/i)
//                                         ? `
//                                             <div class="pdf-card mt-2 border rounded p-2 bg-white">
//                                                 <a
//                                                     href="${message.media_url}"
//                                                     target="_blank"
//                                                     class="text-decoration-none text-dark"
//                                                 >
//                                                     <div class="d-flex align-items-center gap-2">
//                                                         <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
//                                                         <div>
//                                                             <div class="fw-semibold">
//                                                                 PDF Document
//                                                             </div>
//                                                             <small class="text-muted">
//                                                                 Click to open PDF
//                                                             </small>
//                                                         </div>
//                                                     </div>
//                                                 </a>
//                                                 <a
//                                                     href="${message.media_url}"
//                                                     download
//                                                     class="w-full btn btn-outline-success mt-2 mx-auto"
//                                                 >
//                                                     Download
//                                                 </a>
//                                             </div>
//                                         `
//                                         : `
//                                             <a
//                                                 href="${message.media_url}"
//                                                 target="_blank"
//                                                 class="d-block mt-2"
//                                             >
//                                                 Download File
//                                             </a>
//                                         `
//                                     : ''
//                             }
//                             <div class="text-end small mt-1">
//                                 ${tickIcon}
//                             </div>
//                         </div>
//                     </div>
//                 `;
//             });
//             chat_box_admin.scrollTop = chat_box_admin.scrollHeight;
//             markUserMessagesDelivered(docRef, data.chat);
//             if (document.getElementById('chatModal').classList.contains('show')) {
//                 markUserMessagesSeen(docRef, data.chat);
//             }
//         });
//         document.getElementById('chatModal').addEventListener('shown.bs.modal', async () => {
//             const docSnap = await getDoc(docRef);
//             if (docSnap.exists()) {
//                 const data = docSnap.data();
//                 if (data.chat) {
//                     await markUserMessagesSeen(docRef, data.chat);
//                 }
//             }
//         });
//     }
// });

// // Handle reply send
// const sendReplyButton = document.getElementById('send-admin-reply');
// if (sendReplyButton) {
//     sendReplyButton.addEventListener('click', async () => {
//         const replyInput = document.getElementById('admin-reply-message');
//         const mediaInput = document.getElementById('admin-chat-media');

//         const reply = replyInput.value;
//         const file = mediaInput.files[0];
//         let mediaUrl = '';

//         if (reply.trim() === '' && !file && !adminAudioBlob) return;

//         try {
//             if (file || adminAudioBlob) {
//                 const formData = new FormData();
//                 formData.append('chat-media', file || adminAudioBlob, file ? file.name : 'voice-message.webm');

//                 const response = await fetch('/chat/upload', {
//                     method: 'POST',
//                     body: formData,
//                     headers: {
//                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//                     }
//                 });

//                 const data = await response.json();
//                 mediaUrl = data.url;
//             }

//             const docRef = doc(db, 'messages', `user_${selectedUserId}`);

//             await setDoc(docRef, { user_id: selectedUserId }, { merge: true });
//             await updateDoc(docRef, {
//                 chat: arrayUnion({
//                     sender: 'admin',
//                     text: reply,
//                     media_url: mediaUrl,
//                     created_at: Timestamp.now(),
//                     status: 'sent'
//                 })
//             });

//             replyInput.value = '';
//             mediaInput.value = '';
//             adminAudioBlob = null;
//             adminAudioPreview.src = '';
//             adminAudioPreview.classList.add('d-none');
//             document.getElementById('admin-media-preview').innerHTML = '';
//         } catch (error) {
//             console.error(error);
//         }
//     });
// }

// // Media preview
// document.getElementById('admin-chat-media').addEventListener('change', function() {
//     const files = this.files;
//     const preview = document.getElementById('admin-media-preview');
//     preview.innerHTML = '';

//     Array.from(files).forEach(file => {
//         if (file.type.startsWith('image/')) {
//             const reader = new FileReader();
//             reader.onload = e => {
//                 const img = document.createElement('img');
//                 img.src = e.target.result;
//                 img.classList.add('img-thumbnail', 'me-2');
//                 img.style.height = '50px';
//                 preview.appendChild(img);
//             };
//             reader.readAsDataURL(file);
//         } else {
//             const badge = document.createElement('span');
//             badge.classList.add('badge', 'bg-secondary', 'me-2');
//             badge.textContent = file.name;
//             preview.appendChild(badge);
//         }
//     });
// });

// const adminChatModal = document.getElementById('chatModal');

// adminChatModal?.addEventListener('shown.bs.modal', async () => {
//     if (!selectedUserId) return;
//     const userDocRef = doc(db, 'messages', `user_${selectedUserId}`);

//     await updateDoc(userDocRef, {
//         admin_status: 'online',
//         admin_last_seen: Timestamp.now()
//     });
// });

// adminChatModal?.addEventListener('hidden.bs.modal', async () => {
//     if (!selectedUserId) return;
//     const userDocRef = doc(db, 'messages', `user_${selectedUserId}`);

//     await updateDoc(userDocRef, {
//         admin_status: 'offline',
//         admin_last_seen: Timestamp.now()
//     });
// });

// document.getElementById('current-location-btn')?.addEventListener('click', () => {
//     if (!selectedUserId) return;
//     navigator.geolocation.getCurrentPosition(async (pos) => {
//         const docRef = doc(db, 'messages', `user_${selectedUserId}`);
//         await setDoc(docRef, {
//             user_id: selectedUserId
//         }, { merge: true });

//         await updateDoc(docRef, {
//             chat: arrayUnion({
//                 sender: 'admin',
//                 type: 'location',
//                 latitude: pos.coords.latitude,
//                 longitude: pos.coords.longitude,
//                 created_at: Timestamp.now(),
//                 status: 'sent'
//             })
//         });

//         adminLocationModal.hide();
//     });
// });

// document.getElementById('custom-location-btn')?.addEventListener('click', async () => {
//     if (!selectedUserId) return;
//     const lat = prompt('Enter latitude:');
//     const lng = prompt('Enter longitude:');

//     if (!lat || !lng) return;
//     const docRef = doc(db, 'messages', `user_${selectedUserId}`);

//     await setDoc(docRef, {
//         user_id: selectedUserId
//     }, { merge: true });

//     await updateDoc(docRef, {
//         chat: arrayUnion({
//             sender: 'admin',
//             type: 'location',
//             latitude: parseFloat(lat),
//             longitude: parseFloat(lng),
//             created_at: Timestamp.now(),
//             status: 'sent'
//         })
//     });

//     adminLocationModal.hide();
// });

// // Initial load
// loadAllUsers();

import { db } from './firebase';

import {
    collection,
    getDocs,
    doc,
    updateDoc,
    arrayUnion,
    onSnapshot,
    Timestamp,
    setDoc,
    getDoc
} from 'firebase/firestore';


let selectedUserId = null;

const tableBody = document.getElementById('chat_table_body');
const chatBoxAdmin = document.getElementById('chat-box-admin');
const locationBtn = document.getElementById('admin-location-btn');


// --------------------------------------------------
// Load all users who have a chat document
// --------------------------------------------------

async function loadAllUsers() {
    try {
        const snapshot = await getDocs(collection(db, 'messages'));

        tableBody.innerHTML = '';

        snapshot.forEach((docSnap) => {
            const data = docSnap.data();

            const userId = data.user_id || docSnap.id.replace('user_', '');

            if (!userId) {
                return;
            }

            const chat = data.chat || [];
            const lastMessage = chat[chat.length - 1];

            let lastMessageText = '';

            if (lastMessage) {
                lastMessageText =
                    lastMessage.text ||
                    lastMessage.message ||
                    (lastMessage.type === 'location'
                        ? 'Location shared'
                        : lastMessage.media_url
                            ? 'Media shared'
                            : '');
            }

            tableBody.innerHTML += `
                <tr>
                    <td>${userId}</td>

                    <td>
                        ${lastMessageText}
                    </td>

                    <td>
                        <button
                            class="btn btn-primary btn-sm reply-btn"
                            data-user-id="${userId}"
                            data-bs-toggle="modal"
                            data-bs-target="#chatModal"
                        >
                            Reply
                        </button>
                    </td>
                </tr>
            `;
        });

    } catch (error) {
        console.error('Error loading users:', error);
    }
}


// --------------------------------------------------
// Mark user messages as delivered
// --------------------------------------------------

async function markUserMessagesDelivered(docRef, chat) {
    const hasUndelivered = chat.some(
        message =>
            message.sender === 'user' &&
            message.status === 'sent'
    );

    if (!hasUndelivered) {
        return;
    }

    const updatedChat = chat.map(message => {
        if (
            message.sender === 'user' &&
            message.status === 'sent'
        ) {
            return {
                ...message,
                status: 'delivered'
            };
        }

        return message;
    });

    try {
        await updateDoc(docRef, {
            chat: updatedChat
        });
    } catch (error) {
        console.error(
            'Error marking messages as delivered:',
            error
        );
    }
}


// --------------------------------------------------
// Mark user messages as seen
// --------------------------------------------------

async function markUserMessagesSeen(docRef, chat) {
    const hasUnseen = chat.some(
        message =>
            message.sender === 'user' &&
            message.status === 'delivered'
    );

    if (!hasUnseen) {
        return;
    }

    const updatedChat = chat.map(message => {
        if (
            message.sender === 'user' &&
            message.status === 'delivered'
        ) {
            return {
                ...message,
                status: 'seen'
            };
        }

        return message;
    });

    try {
        await updateDoc(docRef, {
            chat: updatedChat
        });
    } catch (error) {
        console.error(
            'Error marking messages as seen:',
            error
        );
    }
}


// --------------------------------------------------
// Location modal
// --------------------------------------------------

const locationModalElement =
    document.getElementById('adminLocationOptionsModal');

const adminLocationModal =
    locationModalElement
        ? new bootstrap.Modal(locationModalElement)
        : null;


locationBtn?.addEventListener('click', () => {
    adminLocationModal?.show();
});


// --------------------------------------------------
// Open user chat
// --------------------------------------------------

document.addEventListener('click', (event) => {

    const replyButton =
        event.target.closest('.reply-btn');

    if (!replyButton) {
        return;
    }

    selectedUserId = replyButton.dataset.userId;

    const docRef =
        doc(db, 'messages', `user_${selectedUserId}`);


    onSnapshot(docRef, async (snapshot) => {

        chatBoxAdmin.innerHTML = '';

        if (!snapshot.exists()) {
            return;
        }

        const data = snapshot.data();

        if (!data.chat || !Array.isArray(data.chat)) {
            return;
        }


        // ------------------------------------------
        // User online/offline status
        // ------------------------------------------

        const statusElement =
            document.getElementById('status-indicator');

        if (statusElement) {

            if (data.user_status === 'online') {

                statusElement.innerHTML =
                    '<span class="badge bg-success">Online</span>';

            } else {

                const lastSeen = data.user_last_seen
                    ? data.user_last_seen.toDate().toLocaleString()
                    : 'never';

                statusElement.innerHTML =
                    `<span class="badge bg-secondary">Offline</span>
                     (last seen ${lastSeen})`;
            }
        }


        // ------------------------------------------
        // Sort messages by created_at
        // ------------------------------------------

        const sortedMessages = [...data.chat].sort(
            (a, b) => {

                const aTime =
                    a.created_at?.seconds ||
                    a.created_at ||
                    0;

                const bTime =
                    b.created_at?.seconds ||
                    b.created_at ||
                    0;

                return aTime - bTime;
            }
        );


        // ------------------------------------------
        // Render messages
        // ------------------------------------------

        sortedMessages.forEach((message) => {

            let tickIcon = '';

            // Admin message status
            if (message.sender === 'admin') {

                if (message.status === 'seen') {

                    tickIcon =
                        '<i class="bi bi-check-all text-primary"></i>';

                } else if (message.status === 'delivered') {

                    tickIcon =
                        '<i class="bi bi-check-all text-muted"></i>';

                } else {

                    tickIcon =
                        '<i class="bi bi-check text-muted"></i>';
                }
            }


            // --------------------------------------
            // Location message
            // --------------------------------------

            if (message.type === 'location') {

                chatBoxAdmin.innerHTML += `
                    <div
                        class="d-flex ${
                            message.sender === 'admin'
                                ? 'justify-content-start'
                                : 'justify-content-end'
                        } mb-2"
                    >

                        <div class="location-card shadow-sm">

                            <iframe
                                src="https://maps.google.com/maps?q=${message.latitude},${message.longitude}&z=16&output=embed"
                                width="300"
                                height="180"
                                style="
                                    border:none;
                                    border-radius:12px 12px 0 0;
                                "
                                loading="lazy"
                            ></iframe>

                            <div class="d-flex flex-column p-2 bg-white">

                                <strong>
                                    ${
                                        message.location_name ||
                                        'Shared Location'
                                    }
                                </strong>

                                <a
                                    href="https://www.google.com/maps?q=${message.latitude},${message.longitude}"
                                    target="_blank"
                                    rel="noopener noreferrer"
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


            // --------------------------------------
            // Normal / media message
            // --------------------------------------

            let mediaHtml = '';

            if (message.media_url) {

                const mediaUrl = message.media_url;


                // Image
                if (
                    mediaUrl.match(
                        /\.(jpg|jpeg|png|gif|webp)(\?|$)/i
                    )
                ) {

                    mediaHtml = `
                        <img
                            src="${mediaUrl}"
                            width="150"
                            class="rounded mt-2 d-block"
                            alt="Shared image"
                        />
                    `;

                }


                // Video
                else if (
                    mediaUrl.match(
                        /\.(mp4|mov|avi|mkv|webm)(\?|$)/i
                    )
                ) {

                    mediaHtml = `
                        <video
                            controls
                            width="250"
                            class="mt-2 d-block rounded"
                        >
                            <source src="${mediaUrl}">
                            Your browser does not support video playback.
                        </video>
                    `;

                }


                // Audio
                else if (
                    mediaUrl.match(
                        /\.(mp3|wav|ogg|webm)(\?|$)/i
                    )
                ) {

                    mediaHtml = `
                        <audio
                            controls
                            class="mt-2 d-block"
                        >
                            <source src="${mediaUrl}">
                            Your browser does not support audio playback.
                        </audio>
                    `;

                }


                // PDF
                else if (
                    mediaUrl.match(
                        /\.pdf(\?|$)/i
                    )
                ) {

                    mediaHtml = `
                        <div
                            class="pdf-card mt-2 border rounded p-2 bg-white"
                        >

                            <a
                                href="${mediaUrl}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-decoration-none text-dark"
                            >

                                <div
                                    class="d-flex align-items-center gap-2"
                                >

                                    <i
                                        class="bi bi-file-earmark-pdf-fill text-danger fs-2"
                                    ></i>

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
                                class="w-100 btn btn-outline-success mt-2"
                            >
                                Download
                            </a>

                        </div>
                    `;

                }


                // Other files
                else {

                    mediaHtml = `
                        <a
                            href="${mediaUrl}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="d-block mt-2"
                        >
                            Download File
                        </a>
                    `;
                }
            }


            chatBoxAdmin.innerHTML += `
                <div
                    style="margin-top:10px"
                    class="d-flex ${
                        message.sender === 'admin'
                            ? 'justify-content-start'
                            : 'justify-content-end'
                    }"
                >

                    <div
                        style="max-width:70%;"
                        class="chat-bubble ${
                            message.sender === 'admin'
                                ? 'admin-bubble'
                                : 'user-bubble'
                        }"
                    >

                        <div>
                            ${message.text || message.message || ''}
                        </div>

                        ${mediaHtml}

                        <div class="text-end small mt-1">
                            ${tickIcon}
                        </div>

                    </div>

                </div>
            `;
        });


        // ------------------------------------------
        // Scroll to latest message
        // ------------------------------------------

        chatBoxAdmin.scrollTop =
            chatBoxAdmin.scrollHeight;


        // ------------------------------------------
        // Update message status
        // ------------------------------------------

        await markUserMessagesDelivered(
            docRef,
            data.chat
        );


        const chatModal =
            document.getElementById('chatModal');

        if (
            chatModal &&
            chatModal.classList.contains('show')
        ) {

            await markUserMessagesSeen(
                docRef,
                data.chat
            );
        }

    });
});


// --------------------------------------------------
// Send admin reply
// --------------------------------------------------

const sendReplyButton =
    document.getElementById('send-admin-reply');


if (sendReplyButton) {

    sendReplyButton.addEventListener(
        'click',
        async () => {

            if (!selectedUserId) {
                return;
            }


            const replyInput =
                document.getElementById(
                    'admin-reply-message'
                );

            const mediaInput =
                document.getElementById(
                    'admin-chat-media'
                );


            const reply =
                replyInput?.value.trim() || '';

            const file =
                mediaInput?.files?.[0] || null;


            if (!reply && !file) {
                return;
            }


            let mediaUrl = '';


            try {

                // ----------------------------------
                // Upload media
                // ----------------------------------

                if (file) {

                    const formData =
                        new FormData();

                    formData.append(
                        'chat-media',
                        file,
                        file.name
                    );


                    const response =
                        await fetch(
                            '/chat/upload',
                            {
                                method: 'POST',

                                body: formData,

                                headers: {
                                    'X-CSRF-TOKEN':
                                        document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .content
                                }
                            }
                        );


                    if (!response.ok) {
                        throw new Error(
                            'Media upload failed'
                        );
                    }


                    const uploadData =
                        await response.json();

                    mediaUrl =
                        uploadData.url || '';
                }


                // ----------------------------------
                // Firestore document
                // ----------------------------------

                const docRef =
                    doc(
                        db,
                        'messages',
                        `user_${selectedUserId}`
                    );


                await setDoc(
                    docRef,
                    {
                        user_id: selectedUserId
                    },
                    {
                        merge: true
                    }
                );


                // ----------------------------------
                // Add admin message
                // ----------------------------------

                await updateDoc(
                    docRef,
                    {
                        chat: arrayUnion({

                            sender: 'admin',

                            text: reply,

                            media_url: mediaUrl,

                            created_at:
                                Timestamp.now(),

                            status: 'sent'
                        })
                    }
                );


                // ----------------------------------
                // Reset form
                // ----------------------------------

                if (replyInput) {
                    replyInput.value = '';
                }

                if (mediaInput) {
                    mediaInput.value = '';
                }

                const preview =
                    document.getElementById(
                        'admin-media-preview'
                    );

                if (preview) {
                    preview.innerHTML = '';
                }


            } catch (error) {

                console.error(
                    'Error sending admin message:',
                    error
                );
            }
        }
    );
}


// --------------------------------------------------
// Media preview
// --------------------------------------------------

const adminMediaInput =
    document.getElementById('admin-chat-media');

const adminMediaPreview =
    document.getElementById('admin-media-preview');


adminMediaInput?.addEventListener(
    'change',
    function () {

        if (!adminMediaPreview) {
            return;
        }

        adminMediaPreview.innerHTML = '';


        Array.from(this.files).forEach(
            file => {

                if (file.type.startsWith('image/')) {

                    const reader =
                        new FileReader();


                    reader.onload = event => {

                        const img =
                            document.createElement(
                                'img'
                            );

                        img.src =
                            event.target.result;

                        img.classList.add(
                            'img-thumbnail',
                            'me-2'
                        );

                        img.style.height =
                            '50px';

                        adminMediaPreview.appendChild(
                            img
                        );
                    };


                    reader.readAsDataURL(file);

                } else {

                    const badge =
                        document.createElement(
                            'span'
                        );

                    badge.classList.add(
                        'badge',
                        'bg-secondary',
                        'me-2'
                    );

                    badge.textContent =
                        file.name;

                    adminMediaPreview.appendChild(
                        badge
                    );
                }
            }
        );
    }
);


// --------------------------------------------------
// Admin online status
// --------------------------------------------------

const adminChatModal =
    document.getElementById('chatModal');


adminChatModal?.addEventListener(
    'shown.bs.modal',
    async () => {

        if (!selectedUserId) {
            return;
        }


        const userDocRef =
            doc(
                db,
                'messages',
                `user_${selectedUserId}`
            );


        try {

            await setDoc(
                userDocRef,
                {
                    user_id: selectedUserId,

                    admin_status: 'online',

                    admin_last_seen:
                        Timestamp.now()
                },
                {
                    merge: true
                }
            );

        } catch (error) {

            console.error(
                'Error updating admin status:',
                error
            );
        }
    }
);


// --------------------------------------------------
// Admin offline status
// --------------------------------------------------

adminChatModal?.addEventListener(
    'hidden.bs.modal',
    async () => {

        if (!selectedUserId) {
            return;
        }


        const userDocRef =
            doc(
                db,
                'messages',
                `user_${selectedUserId}`
            );


        try {

            await setDoc(
                userDocRef,
                {
                    user_id: selectedUserId,

                    admin_status: 'offline',

                    admin_last_seen:
                        Timestamp.now()
                },
                {
                    merge: true
                }
            );

        } catch (error) {

            console.error(
                'Error updating admin status:',
                error
            );
        }
    }
);


// --------------------------------------------------
// Admin shares current location
// --------------------------------------------------

document
    .getElementById('current-location-btn')
    ?.addEventListener(
        'click',
        () => {

            if (!selectedUserId) {
                return;
            }


            if (!navigator.geolocation) {

                alert(
                    'Geolocation is not supported by your browser.'
                );

                return;
            }


            navigator.geolocation.getCurrentPosition(

                async (position) => {

                    try {

                        const docRef =
                            doc(
                                db,
                                'messages',
                                `user_${selectedUserId}`
                            );


                        await setDoc(
                            docRef,
                            {
                                user_id:
                                    selectedUserId
                            },
                            {
                                merge: true
                            }
                        );


                        await updateDoc(
                            docRef,
                            {
                                chat: arrayUnion({

                                    sender: 'admin',

                                    type: 'location',

                                    latitude:
                                        position.coords.latitude,

                                    longitude:
                                        position.coords.longitude,

                                    created_at:
                                        Timestamp.now(),

                                    status: 'sent'
                                })
                            }
                        );


                        adminLocationModal?.hide();

                    } catch (error) {

                        console.error(
                            'Error sharing location:',
                            error
                        );
                    }
                },

                (error) => {

                    console.error(
                        'Geolocation error:',
                        error
                    );

                    alert(
                        'Unable to get your current location.'
                    );
                }
            );
        }
    );


// --------------------------------------------------
// Admin shares custom location
// --------------------------------------------------

document
    .getElementById('custom-location-btn')
    ?.addEventListener(
        'click',
        async () => {

            if (!selectedUserId) {
                return;
            }


            const lat =
                prompt('Enter latitude:');

            const lng =
                prompt('Enter longitude:');


            if (!lat || !lng) {
                return;
            }


            const latitude =
                parseFloat(lat);

            const longitude =
                parseFloat(lng);


            if (
                Number.isNaN(latitude) ||
                Number.isNaN(longitude)
            ) {

                alert(
                    'Please enter valid latitude and longitude.'
                );

                return;
            }


            try {

                const docRef =
                    doc(
                        db,
                        'messages',
                        `user_${selectedUserId}`
                    );


                await setDoc(
                    docRef,
                    {
                        user_id:
                            selectedUserId
                    },
                    {
                        merge: true
                    }
                );


                await updateDoc(
                    docRef,
                    {
                        chat: arrayUnion({

                            sender: 'admin',

                            type: 'location',

                            latitude: latitude,

                            longitude: longitude,

                            created_at:
                                Timestamp.now(),

                            status: 'sent'
                        })
                    }
                );


                adminLocationModal?.hide();

            } catch (error) {

                console.error(
                    'Error sharing custom location:',
                    error
                );
            }
        }
    );


// --------------------------------------------------
// Initial load
// --------------------------------------------------

loadAllUsers();
