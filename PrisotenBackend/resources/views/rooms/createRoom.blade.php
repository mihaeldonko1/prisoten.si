@extends('layouts.app')
@section('content')
<button id="createRoomBtn">Create Room</button>
<span id="roomkey"></span>
<button id="joinRoomBtn">Join Room</button>
<div id="joinRoomInputDiv" style="display: none;">
    <input type="text" id="roomCodeInput" placeholder="Enter Room Code">
    <input type="text" id="userNameInput" placeholder="Enter Your Name">
    <button id="joinBtn">Join</button>
</div>

<script>

document.getElementById('createRoomBtn').addEventListener('click', function() {
        var roomCode = Math.random().toString(36).substr(2, 8);
        document.getElementById("roomkey").textContent = roomCode;
        
        axios.post('/create-room', { code: roomCode })
            .then(function(response) {
                console.log('Room created with code:', response.data.roomCode);
                window.Echo.channel('attendanceRoom.' + roomCode)
                    .listen('AttendanceRoom', (e) => {
                        console.log(e.username + ' has joined the room.');
                });
            })
            .catch(function(error) {
                console.error('Error creating room:', error);
            });
    });

    document.getElementById('joinRoomBtn').addEventListener('click', function() {
        document.getElementById('joinRoomInputDiv').style.display = 'block';
    });

    document.getElementById('joinBtn').addEventListener('click', function() {
        var roomCode = document.getElementById('roomCodeInput').value;
        var userName = document.getElementById('userNameInput').value;
        
        axios.post('/join-room', { code: roomCode, name: userName })
            .then(function(response) {
                console.log(response);
                console.log('Joined room with code:', roomCode);
                
            })
            .catch(function(error) {
                console.error('Error joining room:', error);
            });
    });
</script>
@endsection