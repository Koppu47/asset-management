<nav class="app-navbar">
    <div class="nav-container mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        {{-- ฝั่งซ้าย: เมนูหลัก --}}
        <div class="nav-tabs flex min-w-0 flex-1 items-center gap-1 overflow-x-auto py-2 [scrollbar-width:thin]" role="navigation" aria-label="เมนูหลัก">
            @php
                $navigationItems = [['route' => 'assets.index', 'pattern' => 'assets.index', 'label' => 'แคตตาล็อกครุภัณฑ์']];
                if (Auth::user()?->isAdmin()) {
                    $navigationItems = array_merge($navigationItems, [
                        ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'ภาพรวมระบบ'],
                        ['route' => 'admin.inventory-surveys.index', 'pattern' => 'admin.inventory-surveys.*', 'label' => 'สำรวจครุภัณฑ์'],
                        ['route' => 'categories.index', 'pattern' => 'categories.*', 'label' => 'จัดการหมวดหมู่'],
                        ['route' => 'assets.create', 'pattern' => 'assets.create', 'label' => 'เพิ่มครุภัณฑ์'],
                        ['route' => 'admin.borrows.index', 'pattern' => 'admin.borrows.*', 'label' => 'ยืม-คืน'],
                        ['route' => 'admin.reports.assets', 'pattern' => 'admin.reports.*', 'label' => 'รายงาน'],
                        ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'จัดการผู้ใช้งาน'],
                    ]);
                } elseif (Auth::check()) {
                    $navigationItems[] = ['route' => 'borrows.my', 'pattern' => 'borrows.my', 'label' => 'ประวัติการยืม'];
                }
            @endphp

            @foreach ($navigationItems as $item)
                @php
                    $isActive = request()->routeIs($item['pattern'])
                        || ($item['route'] === 'assets.index' && request()->routeIs('assets.show'));
                @endphp
                <a href="{{ route($item['route']) }}" @class([
                    'nav-tab-item inline-flex min-h-10 shrink-0 items-center whitespace-nowrap rounded-lg px-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1',
                    'active bg-white text-indigo-700 shadow-sm ring-1 ring-slate-200' => $isActive,
                    'text-slate-600 hover:bg-white hover:text-slate-900' => ! $isActive,
                ]) @if ($isActive) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        {{-- ฝั่งขวา: ส่วนผู้ใช้ (Profile / Login / Register) --}}
        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
            @auth
                @if (Auth::user()->isAdmin())
                    <span class="hidden rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-800 ring-1 ring-violet-200 md:inline-flex">เจ้าหน้าที่พัสดุ</span>
                @else
                    <span class="hidden rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-800 ring-1 ring-sky-200 md:inline-flex">พนักงาน</span>
                @endif
                <x-dropdown align="right" width="w-56">
                    <x-slot name="trigger">
                        <button class="inline-flex max-w-52 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4.5 20a7.5 7.5 0 0 1 15 0" /></svg>
                            </span>
                            <span class="truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 20 20" aria-hidden="true"><path fill="currentColor" fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.09 1.04l-4.25 4.5a.75.75 0 0 1-1.09 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">โปรไฟล์ของฉัน</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-700 transition hover:bg-rose-50 focus:bg-rose-50 focus:outline-none">ออกจากระบบ</button>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">เข้าสู่ระบบ</a>
                <a href="{{ route('register') }}" class="inline-flex min-h-10 items-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">ลงทะเบียน</a>
            @endauth
        </div>
    </div>
</nav>
