
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create room') }}
        </h2>
    </x-slot>
            <div class="container">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 text-center" style="background-color:white; height: 50vh; margin-top: 30px; border-radius: 5px">
                        <button id="createRoomBtn" class="btn btn-dark mt-3">Create Room</button><br>
                        <div id="timer" class="timer-container" style="display: none;justify-content: center;">
                            <div class="circle">
                                <svg width="200" height="200">
                                    <circle cx="100" cy="100" r="90" class="background-circle"></circle>
                                    <circle cx="100" cy="100" r="90" class="foreground-circle" id="foregroundCircle"></circle>
                                </svg>
                                <div class="timer-text" id="timerText">0:00</div>
                            </div>
                            <div>
                                <button onclick="addFiveMinutes()">+5 Minutes</button>
                            </div>
                        </div>
                <span>Your room code:</span><br><span id="roomkey"></span><br>
                <div class="qrcode-row">
                    <canvas id="qrcode"></canvas>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
        











        {{--<button id="joinRoomBtn">Join Room</button>
        <div id="joinRoomInputDiv" style="display: none;">
            <input type="text" id="roomCodeInput" placeholder="Enter Room Code">
            <input type="text" id="userNameInput" placeholder="Enter Your Name">
            <button id="joinBtn">Join</button>
        </div>--}}
    </div>
    

<script>

function generateQR(code){
    QRCode.toCanvas(document.getElementById('qrcode'), code, { errorCorrectionLevel: 'H' }, function (error) {
            if (error) console.error(error);
            console.log('QR code generated successfully!');
        });
}

document.getElementById('createRoomBtn').addEventListener('click', function() {
        var roomCode = Math.random().toString(36).substr(2, 8);
        document.getElementById("roomkey").textContent = roomCode;
        
        axios.post('/create-room', { code: roomCode })
            .then(function(response) {
                startTimer();
                document.getElementById("timer").style.display = "grid";
                console.log('Room created with code:', response.data.roomCode);
                generateQR(roomCode);
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
<script>
        let timer;
        let totalTime;
        let timeLeft;
        const circleLength = 2 * Math.PI * 90; 

        function startTimer() {
            const minutes = 10;
            if (isNaN(minutes) || minutes <= 0) {
                alert("Please enter a valid number of minutes.");
                return;
            }
            totalTime = minutes * 60;
            timeLeft = totalTime;
            const timerText = document.getElementById('timerText');
            const foregroundCircle = document.getElementById('foregroundCircle');

            if (timer) {
                clearInterval(timer);
            }

            updateTimerText(timeLeft);
            foregroundCircle.style.transition = 'none';
            foregroundCircle.style.strokeDashoffset = circleLength;
            
            // Ensure the browser renders the initial state before applying the transition
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    foregroundCircle.style.transition = 'stroke-dashoffset 1s linear';
                    foregroundCircle.style.strokeDashoffset = circleLength * (timeLeft / totalTime);
                });
            });

            timer = setInterval(() => {
                timeLeft--;
                updateTimerText(timeLeft);
                foregroundCircle.style.strokeDashoffset = circleLength * (timeLeft / totalTime);

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timerText.textContent = 'Timer finished';
                    foregroundCircle.style.strokeDashoffset = 0;
                }
            }, 1000);
        }

        function addFiveMinutes() {
            if (timeLeft <= 0) {
                alert("Timer has already finished.");
                return;
            }
            totalTime += 5 * 60; // Add 5 minutes to the total time
            timeLeft += 5 * 60;  // Add 5 minutes to the remaining time
            updateTimerText(timeLeft);

            const foregroundCircle = document.getElementById('foregroundCircle');
            foregroundCircle.style.transition = 'none';
            foregroundCircle.style.strokeDashoffset = circleLength * (timeLeft / totalTime);
            
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    foregroundCircle.style.transition = 'stroke-dashoffset 1s linear';
                    foregroundCircle.style.strokeDashoffset = circleLength * (timeLeft / totalTime);
                });
            });
        }

        function updateTimerText(time) {
            const minutes = Math.floor(time / 60);
            const seconds = time % 60;
            document.getElementById('timerText').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    </script>
</x-app-layout>
