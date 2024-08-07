<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Subject data') }}
        </h2>
    </x-slot>

<div class="container mt-5">
    <div class="card mb-4">
        <div class="card-header">
            <h2>{{ $subject['name'] }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Group:</strong> {{ $group['name'] ?? 'N/A' }}</p>
            <p><strong>Year:</strong> {{ $subject['year'] ?? 'N/A' }}</p>
            <p><strong>Total Expected Students:</strong> {{ count($expected_students) }}</p>
            <p><strong>Lessons so far:</strong> {{ $totalHours }}</p>
            <a href="/statistics?group={{ $group['id'] }}&subject={{ $subject['id'] }}" class="btn btn-primary mt-3">Check out previous lessons</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-5">
            <h3 class="mb-3">Expected students:</h3>
        </div>
        @foreach($expected_students as $student)
        <div class="col-md-4">
            <div class="card mb-4 position-relative">
                <div class="card-header">
                    <h5 class="card-title" style="margin: 0 !important">{{ $student['name'] }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $student['email'] }}</p>
                    <p><strong>Hours attended:</strong> {{ $student['attendance_count'] }}</p>
                    <p><strong>Attendance Percentage:</strong> {{ number_format($student['attendance_percentage'], 2) }}%</p>
                </div>
                <div class="attendance-dot" data-percentage="{{ $student['attendance_percentage'] }}"></div>
            </div>
        </div>
        @endforeach

        @if(count($extra_students) > 0)
        <div class="col-12 mt-5">
            <h3 class="mb-3">Extra Students:</h3>
        </div>
        @foreach($extra_students as $student)
        <div class="col-md-4">
            <div class="card mb-4 position-relative">
                <div class="card-header">
                    <h5 class="card-title" style="margin: 0 !important">{{ $student['name'] }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $student['email'] }}</p>
                    <p><strong>Hours attended:</strong> {{ $student['attendance_count'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dots = document.querySelectorAll('.attendance-dot');

        dots.forEach(dot => {
            const percentage = parseFloat(dot.getAttribute('data-percentage'));

            if (percentage >= 80) {
                dot.style.backgroundColor = 'green';
            } else if (percentage >= 60) {
                dot.style.backgroundColor = 'yellow';
            } else {
                dot.style.backgroundColor = 'red';
            }
        });
    });
</script>

</x-app-layout>
