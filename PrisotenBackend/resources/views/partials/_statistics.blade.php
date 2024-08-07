<!-- resources/views/partials/_statistics.blade.php -->

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
