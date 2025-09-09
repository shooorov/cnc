@extends('layouts.back')

@section('title', ($user ? $user->name . '\'s History' : 'History') . ' - User')

@section('header')
    <div class="px-4 sm:px-6 lg:max-w-7xl lg:mx-auto lg:px-8">
        <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
            <div class="flex-1 flex items-center min-w-0 h-10">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="/" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path
                                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                    <span class="sr-only">{{ __('Home') }}</span>
                                </a>
                            </div>
                        </li>

                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 w-5 h-5 text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <a href="{{ route('user.index') }}"
                                    class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('Users') }}</a>
                            </div>
                        </li>

                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 w-5 h-5 text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                                    aria-current="page">{{ __('History Page') }}</a>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <a href="{{ route('user.create') }}"
                    class="inline-flex items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Create') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="px-4 sm:px-6 lg:max-w-7xl lg:mx-auto lg:px-8">
        @include('layouts.partials.alert')

        <div class="bg-white shadow sm:rounded-lg">
            <div class="flex justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
                <p class="max-w-2xl leading-10 text-gray-700 text-lg font-medium">
                    {{ ($user ? $user->name . '\'s ' : '') . __('Histories') }}</p>

                <div class="flex-shrink-0 flex space-x-3">
                    <form class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <div class="text-sm text-gray-700 w-48">
                            <select
                                class="select2 focus:ring-primary-400 focus:border-primary-400 block w-full px-4 sm:text-sm border-gray-300 rounded"
                                name="user" id="user" onchange="this.form.submit()">
                                <option value="">{{ __('Select User') }}</option>
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $filter['user']) selected @endif>
                                        {{ __($item->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <dl class="space-y-8 px-5 py-6">
                    <div class="w-full px-4 py-3 sm:px-6">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($histories as $history)
                                    <li>
                                        <div class="relative pb-8">

                                            @if (count($histories) != $loop->iteration)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex items-start space-x-3">
                                                <div class="relative">
                                                    <img class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                                        src="{{ $history->user->image_path }}" alt="">
                                                    <span
                                                        class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                                                        @if ($history->action == 'login')
                                                            <svg class="h-5 w-5 text-green-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                                                </path>
                                                            </svg>
                                                        @elseif($history->action == 'logout')
                                                            <svg class="h-5 w-5 text-red-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                                </path>
                                                            </svg>
                                                        @elseif($history->action == 'create')
                                                            <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                        @elseif($history->action == 'update')
                                                            <svg class="h-5 w-5 text-modify-400" fill="currentColor"
                                                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                                </path>
                                                                <path fill-rule="evenodd"
                                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif($history->action == 'delete')
                                                            <svg class="h-5 w-5 text-red-400" fill="currentColor"
                                                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif($history->action == 'status')
                                                            <svg class="h-5 w-5 text-blue-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01">
                                                                </path>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div>
                                                        <div class="text-sm">
                                                            <a href="#"
                                                                class="font-medium text-gray-900">{{ $history->user->name }}</a>
                                                        </div>
                                                        <p class="mt-0.5 text-sm text-gray-500">{{ $history->action }} at
                                                            <strong>{{ $history->getCreatedAt() }}</strong></p>
                                                    </div>
                                                    <div class="mt-2 text-sm text-gray-700">
                                                        {{-- <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tincidunt nunc ipsum tempor purus vitae id. Morbi in vestibulum nec varius. Et diam cursus quis sed purus nam.
                                                </p> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </dl>
            </div>

            <div class="bg-white py-8">
                <div class="max-w-lg mx-auto px-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">

                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                        aria-hidden="true"></span>
                                    <div class="relative flex items-start space-x-3">

                                        <div class="relative">
                                            <img class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                                src="https://images.unsplash.com/photo-1520785643438-5bf77931f493?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                                                alt="">

                                            <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                                                <svg class="h-5 w-5 text-gray-400"
                                                    x-description="Heroicon name: solid/chat-alt"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <a href="#" class="font-medium text-gray-900">Eduardo Benz</a>
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500">Commented 6d ago</p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tincidunt nunc
                                                    ipsum tempor purus vitae id. Morbi in vestibulum nec varius. Et diam
                                                    cursus quis sed purus nam.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="relative pb-8">

                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                        aria-hidden="true"></span>
                                    <div class="relative flex items-start space-x-3">

                                        <div>
                                            <div class="relative px-1">
                                                <div
                                                    class="h-8 w-8 bg-gray-100 rounded-full ring-8 ring-white flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-gray-500"
                                                        x-description="Heroicon name: solid/user-circle"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-1.5">
                                            <div class="text-sm text-gray-500">
                                                <a href="#" class="font-medium text-gray-900">Hilary Mahy</a>
                                                <!-- space -->
                                                assigned
                                                <!-- space -->
                                                <a href="#" class="font-medium text-gray-900">Kristin Watson</a>
                                                <!-- space -->
                                                <span class="whitespace-nowrap">2d ago</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="relative pb-8">

                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                        aria-hidden="true"></span>
                                    <div class="relative flex items-start space-x-3">

                                        <div>
                                            <div class="relative px-1">
                                                <div
                                                    class="h-8 w-8 bg-gray-100 rounded-full ring-8 ring-white flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-gray-500"
                                                        x-description="Heroicon name: solid/tag"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-0">
                                            <div class="text-sm leading-8 text-gray-500">
                                                <span class="mr-0.5">
                                                    <a href="#" class="font-medium text-gray-900">Hilary Mahy</a>
                                                    <!-- space -->
                                                    added tags
                                                </span>
                                                <!-- space -->
                                                <span class="mr-0.5">

                                                    <a href="#"
                                                        class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5 text-sm">
                                                        <span
                                                            class="absolute flex-shrink-0 flex items-center justify-center">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                                                aria-hidden="true"></span>
                                                        </span>
                                                        <span class="ml-3.5 font-medium text-gray-900">Bug</span>
                                                    </a>
                                                    <!-- space -->

                                                    <a href="#"
                                                        class="relative inline-flex items-center rounded-full border border-gray-300 px-3 py-0.5 text-sm">
                                                        <span
                                                            class="absolute flex-shrink-0 flex items-center justify-center">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-modify-500"
                                                                aria-hidden="true"></span>
                                                        </span>
                                                        <span class="ml-3.5 font-medium text-gray-900">Accessibility</span>
                                                    </a>
                                                    <!-- space -->

                                                </span>
                                                <span class="whitespace-nowrap">6h ago</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex items-start space-x-3">

                                        <div class="relative">
                                            <img class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                                                src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=8&amp;w=256&amp;h=256&amp;q=80"
                                                alt="">

                                            <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                                                <svg class="h-5 w-5 text-gray-400"
                                                    x-description="Heroicon name: solid/chat-alt"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <a href="#" class="font-medium text-gray-900">Jason Meyers</a>
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500">Commented 2h ago</p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tincidunt nunc
                                                    ipsum tempor purus vitae id. Morbi in vestibulum nec varius. Et diam
                                                    cursus quis sed purus nam. Scelerisque amet elit non sit ut tincidunt
                                                    condimentum. Nisl ultrices eu venenatis diam.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
