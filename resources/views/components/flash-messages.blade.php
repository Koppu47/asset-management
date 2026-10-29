@if (session('success'))
    <div class="alert alert-success">
        <svg class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="font-medium text-sm">{{ session('success') }}</div>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        <svg class="h-5 w-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="font-medium text-sm">{{ session('error') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-warning flex-col">
        <div class="flex items-center space-x-2 font-semibold text-sm mb-2 text-amber-900">
            <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>กรุณาตรวจสอบข้อมูลที่กรอก:</span>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1 text-amber-800 ml-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
