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
                        <h5 class="card-title">ID: {{ $result['id'] }}</h5>
                        <p class="card-text"><strong>User ID:</strong> {{ $result['user_id'] }}</p>
                        <p class="card-text"><strong>Code:</strong> {{ $result['code'] }}</p>
                        <p class="card-text"><strong>Active:</strong> {{ $result['active'] }}</p>
                        <p class="card-text"><strong>Students:</strong> {{ $result['students'] }}</p>
                        <p class="card-text"><strong>Classroom ID:</strong> {{ $result['classroom_id'] }}</p>
                        <p class="card-text"><strong>Closed At:</strong> {{ $result['closed_at'] }}</p>
                        <p class="card-text"><strong>Created At:</strong> {{ $result['created_at'] }}</p>
                        <p class="card-text"><strong>Updated At:</strong> {{ $result['updated_at'] }}</p>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="modal-id"></span></p>
                <p><strong>User ID:</strong> <span id="modal-user-id"></span></p>
                <p><strong>Code:</strong> <span id="modal-code"></span></p>
                <p><strong>Active:</strong> <span id="modal-active"></span></p>
                <p><strong>Students:</strong> <span id="modal-students"></span></p>
                <p><strong>Classroom ID:</strong> <span id="modal-classroom-id"></span></p>
                <p><strong>Closed At:</strong> <span id="modal-closed-at"></span></p>
                <p><strong>Created At:</strong> <span id="modal-created-at"></span></p>
                <p><strong>Updated At:</strong> <span id="modal-updated-at"></span></p>
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
        $('.open_modal').on('click', function() {
            //console.log("opentext")


            

            var result = $(this).data('result');
            axios.post('/getStudentStatistics', { students: result.students})
                    .then(function(response) {
                        console.log('logging res:', response.data);



                        
                        $('#modal-id').text(result.id);
                        $('#modal-user-id').text(result.user_id);
                        $('#modal-code').text(result.code);
                        $('#modal-active').text(result.active);
                        $('#modal-classroom-id').text(result.classroom_id);
                        $('#modal-closed-at').text(result.closed_at);
                        $('#modal-created-at').text(result.created_at);
                        $('#modal-updated-at').text(result.updated_at);



                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
        });
    });
</script>
















</x-app-layout>