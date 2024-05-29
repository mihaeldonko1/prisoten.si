
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create room') }}
        </h2>
    </x-slot>
            <div class="container" >
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 text-center" style="background-color:white; min-height: 50vh; margin-top: 30px; border-radius: 5px">
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
                                <button onclick="addFiveMinutes()" class="btn btn-primary">+5 Minutes</button>
                            </div>
                        </div>
                <span id="room-code-txt" class="font-sans">Your room code:</span><span id="roomkey"></span><br>
                <div class="qrcode-row">
                    <canvas id="qrcode" style="height: 200px;width: 200px"></canvas>
                </div>
                <div id="joined_users" class="row">
                    <h3 id="user_list" style="display: none">List of joined users:</h3>
                </div>
            </div>
        </div>

        








    </div>
    

<script>

function generateQR(code){
    QRCode.toCanvas(document.getElementById('qrcode'), code, { errorCorrectionLevel: 'H' }, function (error) {
            if (error) console.error(error);
            console.log('QR code generated successfully!');
        });
}

document.getElementById('createRoomBtn').addEventListener('click', function() {
    const socket = new WebSocket('wss://localhost:8080');

    socket.onopen = function() {
        console.log('WebSocket connection established');
        createMessage(socket);
    };
    
    socket.onmessage = function(event) {
        const dataObject = JSON.parse(event.data);
        const actionValue = dataObject.action;
        console.log(dataObject);
        if(actionValue == "created"){
                let genRoomCode = dataObject.roomCode;


                axios.post('/schedule-close-websocket', { code: genRoomCode, timeLeft: 15 })
                .then(function(response) {
                    console.log('Close WebSocket job scheduled:', response.data);
                })
                .catch(function(error) {
                    console.error('Error scheduling close WebSocket job:', error);
                });


                axios.post('/create-room', { code: genRoomCode, id: '{{ Auth::user()->id }}' })
                .then(function(response) {
                    console.log('created room x');
                })
                .catch(function(error) {
                    console.error('error room x');
                });


                startTimer();
                document.getElementById("timer").style.display = "grid";
                document.getElementById("room-code-txt").style.display = "block";
                document.getElementById('createRoomBtn').style.display = "none";
                document.getElementById("roomkey").innerHTML = genRoomCode;
                generateQR(genRoomCode);
        }else if(actionValue == "user_joined"){
            document.getElementById("user_list").style.display = "block"
            addUser(dataObject.name, dataObject.email, `{{ asset('cdn/img/360_F_553796090_XHrE6R9jwmBJUMo9HKl41hyHJ5gqt9oz.jpg') }}`);
        }
    };

    socket.onerror = function(error) {
        console.error('WebSocket error: ', error);
    };

    socket.onclose = function(event) {
        console.log('WebSocket connection closed', event);
    };
});

function addUser(name, email, avatarSrc) {
    const userDiv = document.createElement('div');
    userDiv.className = 'col-md-6 d-flex justify-content-center align-items-center';

    const userImage = document.createElement('img');
    userImage.src = avatarSrc;
    userImage.id = 'user-logo';
    userImage.alt = 'User avatar';
    userImage.style.height = '20px';
    userImage.style.width = '20px';
    userImage.style.marginRight = '10px';

    const userInfoDiv = document.createElement('div');
    userInfoDiv.className = 'user-info';

    const userName = document.createElement('h4');
    userName.textContent = name;
    userInfoDiv.appendChild(userName);


    const userEmail = document.createElement('p');
    userEmail.textContent = email;
    userInfoDiv.appendChild(userEmail);

    userDiv.appendChild(userImage);
    userDiv.appendChild(userInfoDiv);

    document.getElementById('joined_users').appendChild(userDiv);
}

// Example usage
function createMessage(socket) {
    const message = {
        "action": "create",
        "name": "Miha Donko",
        "email": "YOUR_EMAIL@example.com",
        "location": {
            "coords": {
                "accuracy": 100,
                "longitude": 15.1573861,
                "altitude": 468.1999816894531,
                "heading": 0,
                "latitude": 46.5909305,
                "altitudeAccuracy": 100,
                "speed": 0
            },
            "mocked": false,
            "timestamp": 1716478829145
        },
        "diameter": 100
    };

    socket.send(JSON.stringify(message));
}
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
