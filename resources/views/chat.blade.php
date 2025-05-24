@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Chat</h3>
    <div id="chat-box" style="border:1px solid #ccc; padding:10px; height:300px; overflow-y:auto;"></div>
    
    <form id="chat-form">
        @csrf
        <input type="hidden" id="receiver_id" value="{{ $receiver->id }}">
        <textarea id="message" class="form-control" placeholder="Type message..."></textarea>
        <button class="btn btn-primary mt-2">Send</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const chatBox = document.getElementById('chat-box');
    const form = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const receiverId = document.getElementById('receiver_id').value;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        axios.post('/api/messages/send', {
            receiver_id: receiverId,
            content: messageInput.value
        }).then(res => {
            messageInput.value = '';
        });
    });

    window.Echo.private('chat.' + receiverId)
        .listen('MessageSent', (e) => {
            const msg = `<div><strong>${e.sender.name}</strong>: ${e.content}</div>`;
            chatBox.innerHTML += msg;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
</script>
@endpush
