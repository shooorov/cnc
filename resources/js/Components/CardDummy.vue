<template>
    <div class="relative">
        <div class="-mr-[4.625rem] w-full rounded-md bg-white p-4 text-[0.8125rem] leading-6 text-slate-900 shadow-xl shadow-black/5 ring-1 ring-slate-700/10">
            <div class="font-semibold leading-5">Account</div>
            <div class="mt-2 leading-5 text-slate-500">Manage how information is displayed on your account.</div>

            <div class="mt-4 flex items-center border-t border-slate-400/20 py-3">
                <span class="w-2/5 flex-none">Language</span>
                <span class="">English</span>
                <span class="pointer-events-auto ml-auto font-medium text-indigo-600 hover:text-indigo-500">Update</span>
            </div>

            <div class="flex items-center border-t border-slate-400/20 py-3">
                <span class="w-2/5 flex-none">Date format</span>
                <span class="">DD-MM-YYYY</span>
                <span class="ml-auto flex items-center font-medium text-indigo-600">
                    <span class="pointer-events-auto hover:text-indigo-500">Update</span>
                    <span class="mx-3 h-6 w-px bg-slate-400/20"></span>
                    <span class="pointer-events-auto hover:text-indigo-500">Remove</span>
                </span>
            </div>

            <div class="flex items-center border-t border-slate-400/20 py-3">
                <span>Automatic timezone</span>
                <span class="ml-auto">
                    <div class="pointer-events-auto h-6 w-10 rounded-full p-1 ring-1 ring-inset transition duration-200 ease-in-out bg-indigo-600 ring-black/20">
                        <div class="h-4 w-4 rounded-full bg-white shadow-sm ring-1 ring-slate-700/10 transition duration-200 ease-in-out translate-x-4"></div>
                    </div>
                </span>
            </div>

            <div class="flex items-center border-t border-slate-400/20 pt-3">
                <span>Auto-update applicant data</span>
                <span class="ml-auto">
                    <div class="pointer-events-auto h-6 w-10 rounded-full p-1 ring-1 ring-inset transition duration-200 ease-in-out bg-slate-900/10 ring-slate-900/5">
                        <div class="h-4 w-4 rounded-full bg-white shadow-sm ring-1 ring-slate-700/10 transition duration-200 ease-in-out"></div>
                    </div>
                </span>
            </div>
        </div>
    </div>
</template>
