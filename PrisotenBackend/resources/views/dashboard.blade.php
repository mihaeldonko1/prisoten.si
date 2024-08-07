<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 mb-3">
                <span class="mb-3">{{ __("Welcome! Your subjects are listed below!") }}</span>
                    

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-3">
                        @foreach($results as $result)
                            <a href="{{ url('/subject/' . $result['subject_group']->id) }}" class="block p-6 max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mb-3 mt-3">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    {{ $result['subject']->name }} - {{ $result['group']->name }}
                                </h5>
                                <p class="font-normal text-gray-700 dark:text-gray-400">
                                    {{ "Year: " . $result['subject']->year }}
                                </p>
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
