@section('title', 'Statistics')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Statistics') }}
        </h2>
    </x-slot>
    <div class='container mx-auto bg-white rounded ml-3 mr-3 mt-3 p-3 dark:bg-gray-800'>
        <h3 class='mb-3'><b>Attendance:</b></h3>
        <div class='row'>
            @foreach ($average as $avg)
                <div class='col-md-4 mb-3'>
                    <div
                        class='bg-white rounded p-3 shadow dark:bg-gray-800 dark:border-gray-700 border border-gray-200'>
                        <p><b>Subject: </b>{{ $avg->subject_name }}</p>
                        <p><b>Group: </b>{{ $avg->school_group_name }}</p>
                        <p><b>Total lessons: </b>{{ $avg->total_lessons }}</p>
                        <p><b>Average expected attendance: </b>{{ number_format($avg->average_expected, 2) }}</p>
                        <p><b>Average attendance: </b>{{ number_format($avg->average_actual, 2) }}</p>
                        <p><b>Average missing: </b>{{ number_format($avg->average_missing, 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class='container mx-auto bg-white rounded ml-3 mr-3 mt-3 p-3'>
        <h3 class='mb-3'><b>Attendance graphs:</b></h3>
        <div class='row'>
            @foreach ($groupedData as $subjectName => $subjectData)
                <div class="col-md-4 mb-4">
                    <div
                        class="chart-container bg-white rounded shadow dark:bg-gray-800 dark:border-gray-700 border border-gray-200">
                        <h4 class="text-center">{{ $subjectName }}</h4>
                        <h6 class="text-center mb-3">Group: {{ $subjectData[0]['school_group_name'] ?? 'N/A' }}</h6>
                        <div id="chart-{{ Str::slug($subjectName, '-') }}" class="ct-chart ct-perfect-fourth"
                            style="height: 250px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const groupedData = @json($groupedData);

            function formatDate(dateString) {
                const date = new Date(dateString);
                const day = date.getDate().toString().padStart(2, '0');
                const month = date.toLocaleString('default', {
                    month: 'short'
                });
                return `${day}-${month}`;
            }

            Object.keys(groupedData).forEach(subjectName => {
                const subjectData = groupedData[subjectName];
                const labels = subjectData.map(item => formatDate(item.date));
                const data = subjectData.map(item => item.student_count);

                const chartId = 'chart-' + subjectName.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                const chartElement = document.getElementById(chartId);

                if (chartElement) {
                    const chartData = {
                        labels: labels,
                        series: [data]
                    };

                    new Chartist.Line(chartElement, chartData, {
                        fullWidth: true,
                        chartPadding: {
                            right: 40
                        },
                        axisX: {
                            labelInterpolationFnc: function(value) {
                                return value.length > 6 ? value.slice(0, 6) + '...' : value;
                            }
                        },
                        axisY: {
                            labelInterpolationFnc: function(value) {
                                return value % 1 === 0 ? value : '';
                            },
                            onlyInteger: true,
                            offset: 20
                        },
                        low: 0
                    });
                } else {
                    console.error('Chart element not found for subject:', subjectName);
                }
            });
        });
    </script>
</x-app-layout>
