@section('title', 'Room creation')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create room') }}
        </h2>
    </x-slot>

        <div id="setupSettings" style="position: fixed; background-color: #808080a8; height: 100vh; width: 100vw;z-index; 10000;display:none">
            <div class="container" style="margin-top: 50px">

                <div class="create-card text-center" style="background-color: white; border-radius: 10px">
                    <div class="row justify-content-end">
                        <img id="close-room-modal" src="{{ asset('cdn/img/75519.png') }}" style="width: 50px;margin-right: 20px;margin-top: 20px;" />
                    </div>
                    <h1 class="mb-3 mt-5" style="font-size: 2.5rem;padding-top: 50px;">Create a custom room</h1>
                    <div class="form-group mb-3" style="padding-left: 150px; padding-right: 150px">
                        <label for="subject_select">Select a subject:</label>
                        <select class="form-select" name="subject_id" id="subject_select">
                            <option value="" selected>Select a subject...</option>
                            @foreach($selectData as $data)

                                <option value="{{ $data['id'] }}" data-fullName="{{ $data['fullName'] }}">{{ $data['fullName'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3" style="padding-left: 150px; padding-right: 150px">
                        <select class="form-select" name="your_select_name" id="your_select_id">
                        <option value="" selected>Select a classroom...</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom['location'] }}" data-id="{{ $classroom['id'] }}">{{ $classroom['name'] }}-{{ $classroom['building'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="slider-container">
                        <label for="slider">Select acceptance range for students:</label>
                        <input type="range" class="form-control-range" id="slider" min="1" max="3000000000" value="1" oninput="updateValue(this.value)">
                        <p>Value: <span id="sliderValue">50</span> meters</p>
                    </div>
                    <button id="createRoomBtn" class="btn btn-dark mt-3" style="margin-bottom: 50px">Create Room</button><br>
                </div>
            </div>
        </div>
        <div class="container" >
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 text-center" style="background-color:white; min-height: 50vh; margin-top: 30px; border-radius: 5px">
                        <button id="openCreateRoomModal" class="btn btn-dark mt-3">Create Room</button><br>
                        <div id="timer" class="timer-container" style="display: none;justify-content: center;">
                            <h1 id="subject-title"></h1>
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js" integrity="sha512-a+SUDuwNzXDvz4XrIcXHuCf089/iJAoN4lmrXJg18XnduKK6YlDHNRalv4yd1N40OKI80tFidF+rqTFKGPoWFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
    const socketUrl = @json(config('app.socket_url'));
    var dataId = null;
    var roomCrafted = null;
    var dataSubjectId = null;
    var dataGroupId = null; 
    var dataFullname = null;

    // Load encryption key and IV from environment variables
    const encryptionKey = @json(env('ENCRYPTION_KEY'));
    const encryptionIV = @json(env('ENCRYPTION_IV'));

    console.log("Encryption Key:", encryptionKey);
    console.log("Encryption IV:", encryptionIV);

    console.log(decrypt("dq4TDeMlsjZ0xAsAJerGOjvvqk2EabxteAw2KI8oRTZNuEY0WfaDOfRT9ulL7Lybd9lqW60mUv1p5CjVyG9qEknHZ49py6E9xbNZTsj9+5xqnDUw5OI19hwU63HMB1qcT3+rK35AMu4mJZgJg+sZBw44xSQlynjwF1yMRy8d4quESykyR3rBRxotT7N5wnUg4jVRz9SrRwLTIdyf1lL3mYxixbqO9afJkCWCC1GgLvw7NoBusOdnusdJHtZyHkau4z6T4F5glIXv3k5Fdla6nNUnzvIFu5H7ACCN46N3pHnb8PY0CC8906wiKgdkkgd7"))

    // Function to encrypt data
function encrypt(text) {
    console.log("Encrypting:", text);
    const encrypted = CryptoJS.AES.encrypt(text, CryptoJS.enc.Hex.parse(encryptionKey), {
        iv: CryptoJS.enc.Hex.parse(encryptionIV),
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    const encryptedBase64 = encrypted.toString(); // AES encrypt returns a Base64 string by default
    console.log("Encrypted message (Base64):", encryptedBase64);
    return encryptedBase64;
}

// Function to decrypt data
function decrypt(ciphertext) {
    console.log("Decrypting (Base64):", ciphertext);
    const bytes = CryptoJS.AES.decrypt(ciphertext, CryptoJS.enc.Hex.parse(encryptionKey), {
        iv: CryptoJS.enc.Hex.parse(encryptionIV),
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7
    });
    const decryptedText = bytes.toString(CryptoJS.enc.Utf8);
    console.log("Decrypted message:", decryptedText);
    return decryptedText;
}

    function updateValue(value) {
        document.getElementById('sliderValue').textContent = value;
    }

    function generateQR(code) {
        QRCode.toCanvas(document.getElementById('qrcode'), code, { errorCorrectionLevel: 'H' }, function (error) {
            if (error) console.error(error);
            console.log('QR code generated successfully!');
        });
    }

    function getSelectedDataFullname(selectElement) {
        return selectElement.options[selectElement.selectedIndex].getAttribute('data-fullname');
    }

    function getSelectedValue(selectElement) {
        return selectElement.options[selectElement.selectedIndex].value;
    }

    document.getElementById('close-room-modal').addEventListener('click', function() { 
        document.getElementById('setupSettings').style.display = "none";
    });

    document.getElementById('openCreateRoomModal').addEventListener('click', function() { 
        document.getElementById('setupSettings').style.display = "block";
    });

    document.getElementById('createRoomBtn').addEventListener('click', function() {
        const socket = new WebSocket(socketUrl);
        console.log("WebSocket connection initiated");

        socket.onopen = function() {
            console.log('WebSocket connection established');
            createMessage(socket);
        };

        socket.onmessage = function(event) {
            console.log("Raw received message:", event.data);
            const decryptedData = decrypt(event.data);
            const dataObject = JSON.parse(decryptedData);
            const actionValue = dataObject.action;
            console.log('Received data:', dataObject);

            if (actionValue == "created") {
                let genRoomCode = dataObject.roomCode;
                roomCrafted = genRoomCode;

                startTimer();
                document.getElementById("timer").style.display = "grid";
                document.getElementById("room-code-txt").style.display = "block";
                document.getElementById('openCreateRoomModal').style.display = "none";
                document.getElementById("roomkey").innerHTML = genRoomCode;
                generateQR(genRoomCode);
                document.getElementById('setupSettings').style.display = "none";
                document.getElementById("subject-title").innerHTML = dataFullname;

                axios.post('/schedule-close-websocket', { code: genRoomCode, timeLeft: timeLeft})
                    .then(function(response) {
                        console.log('Close WebSocket job scheduled:', response.data);
                    })
                    .catch(function(error) {
                        console.error('Error scheduling close WebSocket job:', error);
                    });

                axios.post('/create-room', { code: genRoomCode, id: '{{ Auth::user()->id }}', classroom: dataId, subject: dataSubjectId })
                    .then(function(response) {
                        console.log('Room created successfully');
                    })
                    .catch(function(error) {
                        console.error('Error creating room:', error);
                    });

            } else if (actionValue == "user_joined") {
                console.log("User joined");
                document.getElementById("user_list").style.display = "block";
                addUser(dataObject.name, dataObject.email, `{{ asset('cdn/img/360_F_553796090_XHrE6R9jwmBJUMo9HKl41hyHJ5gqt9oz.jpg') }}`);

                axios.post('/join-room', { name: dataObject.name, email: dataObject.email, code: roomCrafted })
                    .then(function(response) {
                        console.log('User joined room successfully');
                    })
                    .catch(function(error) {
                        console.error('Error joining room:', error);
                    });
            }
        };

        socket.onerror = function(error) {
            console.error('WebSocket error:', error);
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

    function createMessage(socket) {
        var selectElement = document.getElementById('your_select_id');
        var selectedOption = selectElement.options[selectElement.selectedIndex];
        var selectedLocation = selectedOption.value;
        dataId = selectedOption.getAttribute('data-id');
        let parsedLocation = JSON.parse(selectedLocation);

        const subjectSelect = document.querySelector('#subject_select');
        dataSubjectId = getSelectedValue(subjectSelect);
        dataFullname = getSelectedDataFullname(subjectSelect);

        var sliderElement = document.getElementById('slider');
        var diameterValue = sliderElement.value;

        const message = {
            "action": "create",
            "name": "{{ Auth::user()->name }}",
            "email": "{{ Auth::user()->email }}",
            "location": parsedLocation,
            "diameter": diameterValue
        };

        const encryptedMessage = encrypt(JSON.stringify(message));
        console.log('Sending encrypted message:', encryptedMessage);

        socket.send(encryptedMessage);
    }
</script>


<script>
        function updateJobTimer(newTime){
            //TODO implement job timer update
            console.log("new timer" + newTime);
            console.log("room code" + roomCrafted);
            axios.post('/update-close-websocket', { timeLeft: newTime, code: roomCrafted })
            .then(function(response) {
                console.log('Close WebSocket job scheduled:', response.data);
            })
            .catch(function(error) {
                console.error('Error scheduling close WebSocket job:', error);
            });
        }


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
            totalTime += 5 * 60;
            timeLeft += 5 * 60; 
            updateTimerText(timeLeft);

            updateJobTimer(timeLeft);

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