@section('title', 'Previous Lessons')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Previous Lessons') }}
        </h2>
    </x-slot>
        <div class="container mt-3">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label for="subject-group-select" class="form-label">Select Subject Group:</label>
                <select id="subject-group-select" class="form-control">
                    <option value="all" data-group="all" data-subject="all">All Groups</option>
                    @foreach($extra_data as $data)
                        <option value="{{ $data['id'] }}" data-group="{{ $data['group_id'] }}" data-subject="{{ $data['subject_id'] }}">
                            {{ $data['full_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="date-from" class="form-label">Date From:</label>
                <input type="text" id="date-from" class="form-control datepicker" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-md-3">
                <label for="date-to" class="form-label">Date To:</label>
                <input type="text" id="date-to" class="form-control datepicker" placeholder="dd/mm/yyyy">
            </div>
            <div class="col-md-3">
                <button id="filter-button" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </div>


        <div class="container mt-5">
            @include('partials._statistics', ['statistics' => $statistics])
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        var openedRoom;

    $('#addStudentToSession').on('click', function() {
        $('.mainModalContentDetails').hide();
        $('.addStudent').show();
    });

     function getQueryParam(param) {
            let urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        function setFieldValues() {
            var subject = getQueryParam('subject');
            var group = getQueryParam('group');
            var dateFrom = getQueryParam('date-from');
            var dateTo = getQueryParam('date-to');

            if (subject) {
                $('#subject-group-select').val(subject).trigger('change');
            }

            if (group) {
                $('#subject-group-select').val(group).trigger('change');
            }

            if (dateFrom) {
                $('#date-from').datepicker('setDate', dateFrom);
            }

            if (dateTo) {
                $('#date-to').datepicker('setDate', dateTo);
            }
        }

        $('#subject-group-select').select2({
            placeholder: 'Search and select a subject group',
            allowClear: true
        });

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            clearBtn: true
        });

        setFieldValues();

        $(document).on('click', '.open_modal', function() {
            const dataResult = $(this).attr('data-result');
            const result = JSON.parse(dataResult);
            console.log(result);
            openedRoom = result.id;
            loadStudentStatistics(result);
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

        $('#filter-button').on('click', function() {
            filterStatistics();
        });

        function switchModals(hideModal, showModal) {
            $(hideModal).one('hidden.bs.modal', function() {
                $(showModal).modal('show');
            }).modal('hide');
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

        function filterStatistics() {
            var selectedOption = $('#subject-group-select').find('option:selected');
            var groupId = selectedOption.data('group'); 
            var subjectId = selectedOption.data('subject'); 
            var dateFrom = $('#date-from').val(); 
            var dateTo = $('#date-to').val();

            var requestData = {};

            if (subjectId !== "all") {
                requestData.subject = subjectId;
            }

            if (groupId !== "all") {
                requestData.group = groupId;
            }

            if (dateFrom) {
                requestData['date-from'] = dateFrom;
            }

            if (dateTo) {
                requestData['date-to'] = dateTo;
            }

            requestData._token = '{{ csrf_token() }}';

            var queryParams = $.param(requestData);
            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (queryParams ? '?' + queryParams : '');
            history.pushState({path: newUrl}, '', newUrl);

            $('#preloader').show();

            $.ajax({
                url: '/statistics',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    $('.container.mt-5').html(response);
                    reinitializeFunctions();

                    $('#preloader').hide();
                },
                error: function(xhr, status, error) {
                    console.error("Error during AJAX request:", status, error);

                    $('#preloader').hide();
                }
            });
        }

        function loadStudentStatistics(result) {
            axios.post('/getStudentStatistics', { 
                students: result.students, 
                expected_students: result.subject_group.logged_students, 
                classroom: result.classroom_id 
            })
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
        }

        function removeStudentAttendance(studentId, roomId) {
            axios.post('/removeStudentSession', { student: studentId, room: roomId })
                .then(function(response) {
                    const updatedResult = response.data.result[0];
                    if (response.data.hasOwnProperty('extra_students')) {
                        $('#students-extra-box').html(fillStudents(response.data.extra_students, updatedResult.id));
                    }
                    if (response.data.hasOwnProperty('missing_students')) {
                        $('#students-missing-box').html(fillMissingStudents(response.data.missing_students, updatedResult.id));
                    }
                    if (response.data.hasOwnProperty('expected_students')) {
                        $('#students-box').html(fillStudents(response.data.expected_students, updatedResult.id));
                    }
                    changeResults(updatedResult);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        }

        function addStudentAttendance(email, openedRoom){
            console.log(email);
            console.log(openedRoom);
            axios.post('/addStudentSession', { studentMail: email, room: openedRoom })
                .then(function(response) {
                    const updatedResult = response.data.result[0];
                    console.log(updatedResult);
                    if (response.data.hasOwnProperty('extra_students')) {
                        $('#students-extra-box').html(fillStudents(response.data.extra_students, updatedResult.id));
                    }
                    if (response.data.hasOwnProperty('missing_students')) {
                        $('#students-missing-box').html(fillMissingStudents(response.data.missing_students, updatedResult.id));
                    }
                    if (response.data.hasOwnProperty('expected_students')) {
                        $('#students-box').html(fillStudents(response.data.expected_students, updatedResult.id));
                    }
                    changeResults(updatedResult);

                    const $card = $(`.open_modal[data-id='${openedRoom}']`);
                    if ($card.length) {
                        $card.attr('data-result', JSON.stringify(updatedResult));
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
            $('.addStudent').hide();
            $('.mainModalContentDetails').show();
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
                    <div class="students-card" data-student-mail="${student.email}" data-student-id="${student.id}" data-room-id="${roomID}">
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
                    <div class="students-card" data-student-mail="${student.email}" data-student-id="${student.id}" data-room-id="${roomID}">
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

        function reinitializeFunctions() {
            $(document).on('click', '.open_modal', function() {
                const dataResult = $(this).attr('data-result');
                const result = JSON.parse(dataResult);
                console.log(result);
                openedRoom = result.id;
                loadStudentStatistics(result);
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
        }
    });
</script>

</x-app-layout>
