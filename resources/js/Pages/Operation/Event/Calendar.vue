<template>
    <Head title="Create Event" />

    <div>
        <div class="bg-white shadow">
            <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200" >
                    <div class="flex-1 min-w-0">
                        <Breadcrumb :breadcrumbs="breadcrumbs" />
                    </div>
                    <div class="mt-6 h-9 flex space-x-3 md:mt-0 md:ml-4"></div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
					<div
						class="flex flex-col sm:flex-row sm:justify-between items-center px-4 py-5 border-b border-gray-200 sm:px-8">
						<div class="flex-1">
							<h3 class="text-base font-semibold leading-6 text-gray-900">
								<time datetime="2022-01">{{ calendar.month_year }}</time>
							</h3>
							<p class="mt-1 text-sm text-gray-500">
							</p>
						</div>
						<div class="flex-shrink-0 flex space-x-3">
							<div class="flex items-center rounded-md shadow-sm md:items-stretch">
								<button type="button" @click="form.month == 1? (form.month = 12, form.year--) : form.month--" @click.prevent="submit" class="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 bg-white py-2 pl-3 pr-4 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50">
									<span class="sr-only">Previous month</span>
									<ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
								</button>

								<button type="button" @click="form.year = today.year, form.month = today.month" @click.prevent="submit" class="hidden border-t border-b border-gray-300 bg-white px-3.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:relative md:block">Today</button>
								<span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden" />

								<button type="button" @click="form.month == 12? (form.month = 1, form.year++) : form.month++" @click.prevent="submit" class="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 bg-white py-2 pl-4 pr-3 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50">
									<span class="sr-only">Next month</span>
									<ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
								</button>
							</div>

							<div class="hidden md:ml-4 md:flex md:items-center">
								<Menu as="div" class="relative">
									<MenuButton type="button" class="flex items-center rounded-md border border-gray-300 bg-white py-2 pl-3 pr-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
										{{ form.year }}
										<ChevronDownIcon class="ml-2 h-5 w-5 text-gray-400" aria-hidden="true" />
									</MenuButton>

									<transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
										<MenuItems class="focus:outline-none absolute right-0 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
											<div class="py-1">
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']" @click="form.year--" @click.prevent="submit">{{ (form.year - 1) }}</a>
											</MenuItem>
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm bg-primary-50']">{{ form.year }}</a>
											</MenuItem>
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']" @click="form.year++" @click.prevent="submit">{{ (parseInt(form.year) + 1) }}</a>
											</MenuItem>
											</div>
										</MenuItems>
									</transition>
								</Menu>

								<div class="ml-6 h-6 w-px bg-gray-300" />
								<Link :href="route('event.create')" class="focus:outline-none ml-6 rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Add event</Link>
							</div>

							<Menu as="div" class="relative ml-6 md:hidden">
								<MenuButton class="-mx-2 flex items-center rounded-full border border-transparent p-2 text-gray-400 hover:text-gray-500">
									<span class="sr-only">Open menu</span>
									<EllipsisHorizontalIcon class="h-5 w-5" aria-hidden="true" />
								</MenuButton>

								<transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
									<MenuItems class="focus:outline-none absolute right-0 mt-3 w-36 origin-top-right divide-y divide-gray-100 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
										<div class="py-1">
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Create event</a>
											</MenuItem>
										</div>
										<div class="py-1">
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">Go to today</a>
											</MenuItem>
										</div>
										<div class="py-1">
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">{{ (form.year - 1) }}</a>
											</MenuItem>
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">{{ form.year }}</a>
											</MenuItem>
											<MenuItem v-slot="{ active }">
												<a href="#" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">{{ (parseInt(form.year) + 1) }}</a>
											</MenuItem>
										</div>
									</MenuItems>
								</transition>
							</Menu>
						</div>
					</div>

					<Alert />

                    <div class="lg:flex lg:h-full lg:flex-col">
                        <div class="shadow ring-1 ring-black ring-opacity-5 lg:flex lg:flex-auto lg:flex-col">
                            <div class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs font-semibold leading-6 text-gray-700 lg:flex-none">
                                <div class="bg-white py-2">S<span class="sr-only sm:not-sr-only">un</span></div>
                                <div class="bg-white py-2">M<span class="sr-only sm:not-sr-only">on</span></div>
                                <div class="bg-white py-2">T<span class="sr-only sm:not-sr-only">ue</span></div>
                                <div class="bg-white py-2">W<span class="sr-only sm:not-sr-only">ed</span></div>
                                <div class="bg-white py-2">T<span class="sr-only sm:not-sr-only">hu</span></div>
                                <div class="bg-white py-2">F<span class="sr-only sm:not-sr-only">ri</span></div>
                                <div class="bg-white py-2">S<span class="sr-only sm:not-sr-only">at</span></div>
                            </div>
                            <div class="flex bg-gray-200 text-xs leading-6 text-gray-700 lg:flex-auto">
                                <div class="hidden w-full lg:grid lg:grid-cols-7 lg:grid-rows-6 lg:gap-px">
                                    <div v-for="day in days" :key="day.date" :class="[day.isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-500', 'relative py-2 px-3', day.isToday? 'bg-indigo-50' : '']">
                                        <time :datetime="day.date" :class="day.isToday ? 'flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white' : undefined">{{ day.date.split('-').pop().replace(/^0/, '') }}</time>
                                        <ol v-if="day.events.length > 0" class="mt-2">
                                            <li v-for="event in day.events.slice(0, 2)" :key="event.id" :title="event.title">
                                                <a :href="event.href" class="group flex" target="_blank">
                                                    <p class="flex-auto truncate font-medium text-gray-900 group-hover:text-indigo-600">
                                                        {{ event.name }}
                                                    </p>
                                                    <time :datetime="event.time_duration" class="ml-3 hidden flex-none text-gray-500 group-hover:text-indigo-600 xl:block">{{ event.time_duration }}</time>
                                                </a>
                                            </li>
                                            <li v-if="day.events.length > 2" class="text-gray-500">+ {{ day.events.length - 2 }} more</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="isolate grid w-full grid-cols-7 grid-rows-6 gap-px lg:hidden">
                                    <button v-for="day in days" :key="day.date" type="button" :class="[day.isCurrentMonth ? 'bg-white' : 'bg-gray-50', (day.isSelected || day.isToday) && 'font-semibold', day.isSelected && 'text-white', !day.isSelected && day.isToday && 'text-indigo-600', !day.isSelected && day.isCurrentMonth && !day.isToday && 'text-gray-900', !day.isSelected && !day.isCurrentMonth && !day.isToday && 'text-gray-500', 'flex h-14 flex-col py-2 px-3 hover:bg-gray-100 focus:z-10']">
                                        <time :datetime="day.date" :class="[day.isSelected && 'flex h-6 w-6 items-center justify-center rounded-full', day.isSelected && day.isToday && 'bg-indigo-600', day.isSelected && !day.isToday && 'bg-gray-900', 'ml-auto']">{{ day.date.split('-').pop().replace(/^0/, '') }}</time>
                                        <p class="sr-only">{{ day.events.length }} events</p>
                                        <div v-if="day.events.length > 0" class="-mx-0.5 mt-auto flex flex-wrap-reverse">
                                            <div v-for="event in day.events" :key="event.id" class="mx-0.5 mb-1 h-1.5 w-1.5 rounded-full bg-gray-400" />
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="selectedDay?.events.length > 0" class="py-10 px-4 sm:px-6 lg:hidden">
                            <ol class="divide-y divide-gray-100 overflow-hidden rounded-lg bg-white text-sm shadow ring-1 ring-black ring-opacity-5">
                                <li v-for="event in selectedDay.events" :key="event.id" class="group flex p-4 pr-6 focus-within:bg-gray-50 hover:bg-gray-50">
                                    <div class="flex-auto">
                                        <p class="font-semibold text-gray-900">{{ event.name }}</p>
                                        <time :datetime="event.time_duration" class="mt-2 flex items-center text-gray-700">
                                            <ClockIcon class="mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                            {{ event.time_duration }}
                                        </time>
                                    </div>
                                    <a :href="event.href" class="ml-6 flex-none self-center rounded-md border border-gray-300 bg-white py-2 px-3 font-semibold text-gray-700 opacity-0 shadow-sm hover:bg-gray-50 focus:opacity-100 group-hover:opacity-100">
                                        Edit<span class="sr-only">, {{ event.name }}</span>
                                    </a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive } from 'vue';
import { router, Head, Link } from '@inertiajs/vue3';

import Alert from '@/Components/Alert.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import { ChevronDownIcon, ChevronLeftIcon, ChevronRightIcon, ClockIcon, EllipsisHorizontalIcon } from '@heroicons/vue/24/solid';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';

import {
    PlusIcon
} from "@heroicons/vue/24/solid";

import {
    PencilSquareIcon
} from "@heroicons/vue/24/outline";

export default {
    layout: AuthenticatedLayout,

    components: {
        Alert,
        Breadcrumb,
        Head,
        InputError,
        Link,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,

        ChevronDownIcon,
        ChevronLeftIcon,
        ChevronRightIcon,
        ClockIcon,
        EllipsisHorizontalIcon,
        PlusIcon,
        PencilSquareIcon,
    },

    props: {

        days: Array,
        selectedDay: Object,
        calendar: Object,
        today: Object,
    },

    setup(props) {
        const breadcrumbs = [
            { name: 'Events', href: route('event.index'), current: false },
            { name: 'Calendar Page', href: '#', current: false },
        ];

        const form = reactive({
            command: null,
            year: props.calendar.year,
            month: props.calendar.month,
        });

        function submit() {
            router.visit(route('event.calendar'), {
  				data: form,
			});
        }

        return {
            breadcrumbs,
            form,
            submit,
        };
    },
}
</script>
