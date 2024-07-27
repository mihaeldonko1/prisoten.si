<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Statistics') }}
        </h2>

    </x-slot>
    <div class="container mt-5">
    <h2>Statistics</h2>
    <div class="row">
        @foreach($statistics as $result)
            <div class="col-md-4 mb-4">
                <div class="card open_modal" data-toggle="modal" data-target="#dataModal" data-result="{{ json_encode($result) }}">
                    <div class="card-body">
                        <p class="card-text"><strong>Room code:</strong> {{ $result['code'] }}</p>
                        <p class="card-text"><strong>Number of participants:</strong>
                        @php
                            $students = json_decode($result['students'], true);
                            $studentsCount = is_array($students) ? count($students) : 0;
                        @endphp
                        <span id="student-count-original">{{ $studentsCount }}</span>
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

<!-- Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Details</h5>
            </div>
            <div class="modal-body">
                <p><strong>Code:</strong> <span id="modal-code"></span></p>
                <p><strong>Present Students:</strong> <span id="modal-students"></span></p>
                <div id="students-box"></div>
                <p><strong>Classroom:</strong> <span id="modal-classroom-id"></span></p>
                <p><strong>Date:</strong> <span id="modal-date"></span></p>
                <p><strong>Time:</strong> <span id="modal-time"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
       function removeStudentAttendance(studentId, roomId) {
            axios.post('/removeStudentSession', { student: studentId, room: roomId})
                .then(function(response) {
                    fillStudents(response.data.students, response.data.roomId);
                    $('#student-count-original').text(response.data.students.length);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
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

            $('#students-box').html(studentsDisplay);
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
            var result = $(this).data('result');
            axios.post('/getStudentStatistics', { students: result.students, classroom: result.classroom_id})
                .then(function(response) {
                    fillStudents(response.data.students, result.id);
                    fillClassroom(response.data.classroom);
                    fillDates(result.created_at, result.closed_at);
                    $('#modal-code').text(result.code);
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
    });
</script>
















</x-app-layout>