<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Previous Sessions') }}
        </h2>
    </x-slot>

    <div class="container mt-5">
        <div class="row">
            @foreach($statistics as $result)
                <div class="col-md-4 mb-4">
                    <div class="card open_modal" data-toggle="modal" data-target="#dataModal" data-id="{{$result['id']}}" data-result="{{ json_encode($result) }}">
                        <div class="card-body">
                            <p class="card-text"><strong>Room code:</strong> {{ $result['code'] }}</p>
                            <p class="card-text"><strong>Subject:</strong> {{ $result['subject'] }}</p>
                            <p class="card-text"><strong>Group:</strong> {{ $result['school_group'] }}</p>
                            <p class="card-text"><strong>Number of participants:</strong>
                            @php
                                $students = json_decode($result['students'], true);
                                $studentsCount = is_array($students) ? count($students) : 0;
                            @endphp
                            <span id="student-count-original{{$result['id']}}">{{ $studentsCount }}</span>
                            </p>
                            <p class="card-text"><strong>Date:</strong>
                            @php
                                $date = \Carbon\Carbon::parse($result['closed_at'])->format('d/m/Y');
                            @endphp
                            {{ $date }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Modal -->
    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Add Student Content -->
                <div class="addStudent">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    </div>
                    <div class="modal-body">
                        <form id="addStudentForm">
                            <div class="form-group">
                                <label for="studentEmail">Student Email</label>
                                <input type="email" class="form-control" id="studentEmail" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submitStudent" data-room="">Submit</button>
                        <button type="button" class="btn btn-secondary" id="cancelSubmitStudent">Cancel</button>
                    </div>
                </div>
                <!-- Main Modal Content Details -->
                <div class="mainModalContentDetails">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dataModalLabel">Details</h5>
                    </div>
                    <div class="modal-body">
                        <p><strong>Code:</strong> <span id="modal-code"></span></p>
                        <p><strong>Subject:</strong> <span id="modal-subject"></span></p>
                        <p><strong>Group:</strong> <span id="modal-group"></span></p>
                        <p><strong>Expected members:</strong> <span id="modal-max-members"></span></p>
                        <p><strong>Present expected students:</strong> <span id="modal-students"></span></p>
                        <div id="students-box"></div>
                        <p><strong>Mising students:</strong> <span id="modal-students"></span></p>
                        <div id="students-missing-box"></div>
                        <p><strong>Extra students:</strong> <span id="modal-students"></span></p>
                        <div id="students-extra-box"></div>
                        <p><strong>Classroom:</strong> <span id="modal-classroom-id"></span></p>
                        <p><strong>Date:</strong> <span id="modal-date"></span></p>
                        <p><strong>Time:</strong> <span id="modal-time"></span></p>
                    </div>
                    <div class="modal-footer" id="modal-footer-btns">
                        <button type="button" class="btn btn-success" id="addStudentToSession">Add Student Presence</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            var openedRoom;

            function removeStudentAttendance(studentId, roomId) {
                axios.post('/removeStudentSession', { student: studentId, room: roomId })
                    .then(function(response) {
                        const updatedResult = response.data.result[0];
                            if (response.data.hasOwnProperty('extra_students')) {
                                $('#students-extra-box').html(fillStudents(response.data.extra_students, response.data.result[0].id));
                            }

                            if (response.data.hasOwnProperty('missing_students')) {
                                $('#students-missing-box').html(fillMissingStudents(response.data.missing_students, response.data.result[0].id));
                            }

                            if (response.data.hasOwnProperty('expected_students')) {
                                $('#students-box').html(fillStudents(response.data.expected_students, response.data.result[0].id));
                            }

                            changeResults(updatedResult); 
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
            }

            function changeResults(newResult) {
                const $div = $(`.open_modal[data-id='${newResult.id}']`);
                if ($div.length) {
                    $div.attr('data-result', JSON.stringify(newResult));
                } else {
                    console.error('No element found with the specified data-id.');
                }
            }

            function fillStudents(studentsArray, roomID) {
                let studentsDisplay = "";
                studentsArray.forEach(function(student) {
                    studentsDisplay += `
                        <div class="students-card" data-student-id="${student.id}" data-room-id="${roomID}">
                            <div class="remove-circle">X</div>
                            <div class="green-circle"></div>
                            <div class="student-info">
                                <div class="student-fullname">${student.name}</div>
                                <div class="student-mail">${student.email}</div>
                            </div>
                        </div>
                    `;
                });

                return studentsDisplay;
                
            }

            function fillMissingStudents(studentsArray, roomID) {
                let studentsDisplay = "";
                studentsArray.forEach(function(student) {
                    studentsDisplay += `
                        <div class="students-card" data-student-id="${student.email}" data-room-id="${roomID}">
                            <div class="add-circle">+</div>
                            <div class="red-circle"></div>
                            <div class="student-info">
                                <div class="student-fullname">${student.name}</div>
                                <div class="student-mail">${student.email}</div>
                            </div>
                        </div>
                    `;
                });

                return studentsDisplay;
                
            }

            function fillClassroom(classroomData) {
                let classroomFullname = classroomData.building + "-" + classroomData.name;
                $('#modal-classroom-id').text(classroomFullname);
            }

            function fillDates(created, closed) {
                let datePart = created.split(' ')[0];
                let [year, month, day] = datePart.split('-');
                let formattedDate = `${day}/${month}/${year}`;
                $('#modal-date').text(formattedDate);

                let timeStarted = created.split(' ')[1];
                let timeFinished = closed.split(' ')[1];
                let fullTime = timeStarted + " - " + timeFinished;
                $('#modal-time').text(fullTime);
            }

            $('.open_modal').on('click', function() {
                const dataResult = $(this).attr('data-result');
                const result = JSON.parse(dataResult);
                console.log(result);
                openedRoom = result.id;
                axios.post('/getStudentStatistics', { students: result.students, expected_students: result.subject_group.logged_students, classroom: result.classroom_id })
                    .then(function(response) {
                        $('#students-box').html(fillStudents(response.data.joinedStudents, result.id));
                        $('#students-extra-box').html(fillStudents(response.data.extraStudents, result.id));
                        $('#students-missing-box').html(fillMissingStudents(response.data.missingStudents, result.id));
        
                        fillClassroom(response.data.classroom);
                        fillDates(result.created_at, result.closed_at);
                        $('#modal-code').text(result.code);
                        $('#modal-max-members').text(result.expected_students_joined_count+"/"+result.expected_students_count);
                        $('#modal-group').text(result.school_group);
                        $('#modal-subject').text(result.subject);
                        $('#modal-students').text(response.data.joinedStudents.length);
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
            });

            $(document).on('click', '.remove-circle', function() {
                let studentId = $(this).closest('.students-card').data('student-id');
                let roomId = $(this).closest('.students-card').data('room-id');
                removeStudentAttendance(studentId, roomId);
            });

            $(document).on('click', '.add-circle', function() {
                let studentId = $(this).closest('.students-card').data('student-id');
                let roomId = $(this).closest('.students-card').data('room-id');
                addStudentAttendance(studentId, roomId);
            });

            $('#addStudentToSession').on('click', function() {
                $('#dataModal').modal('hide');
                $('#addStudentModal').modal('show');
            });

            function switchModals(hideModal, showModal) {
                $(hideModal).one('hidden.bs.modal', function() {
                    $(showModal).modal('show');
                }).modal('hide');
            }

            $('.addStudent').hide();

            $('#addStudentToSession').on('click', function() {
                $('.mainModalContentDetails').hide();
                $('.addStudent').show();
            });

            function addStudentAttendance(email, openedRoom){
                axios.post('/addStudentSession', { studentMail: email, room: openedRoom })
                        .then(function(response) {
                            console.log(response.data.result[0]);
                            const updatedResult = response.data.result[0];
                            if (response.data.hasOwnProperty('extra_students')) {
                                $('#students-extra-box').html(fillStudents(response.data.extra_students, response.data.result[0].id));
                            }

                            if (response.data.hasOwnProperty('missing_students')) {
                                $('#students-missing-box').html(fillMissingStudents(response.data.missing_students, response.data.result[0].id));
                            }

                            if (response.data.hasOwnProperty('expected_students')) {
                                $('#students-box').html(fillStudents(response.data.expected_students, response.data.result[0].id));
                            }

                            changeResults(updatedResult); 

                            const $card = $(`.open_modal[data-id='${openedRoom}']`);
                            if ($card.length) {
                                let cardDataResult = response.data.result[0];

                                $card.attr('data-result', JSON.stringify(cardDataResult));
                            }
                        })
                        .catch(function(error) {
                            console.error('Error:', error);
                        });
                    $('.addStudent').hide();
                    $('.mainModalContentDetails').show();
            }

            $('#submitStudent').on('click', function() {
                var email = $('#studentEmail').val();
                if (email) {
                    addStudentAttendance(email, openedRoom);
                    $('.addStudent').hide();
                    $('.mainModalContentDetails').show();
                } else {
                    alert('Please enter a valid email address.');
                }
            });

            $('#cancelSubmitStudent').on('click', function() {
                $('.addStudent').hide();
                $('.mainModalContentDetails').show();
            });
        });
    </script>
</x-app-layout>
