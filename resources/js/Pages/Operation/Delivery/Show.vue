<script setup>
import { ref } from 'vue';
import { router, Head, Link, usePage } from '@inertiajs/vue3';
import {
	Dialog,
	DialogPanel,
	DialogTitle,
	DialogOverlay,
	Menu,
	MenuButton,
	MenuItem,
	MenuItems,
	TransitionChild,
	TransitionRoot
} from '@headlessui/vue';
import {
	Bars3BottomLeftIcon,
	PlusIcon,
	BriefcaseIcon,
	ArrowLeftOnRectangleIcon,
	HomeIcon,
	TruckIcon,
	XMarkIcon,
} from '@heroicons/vue/24/outline';
import { CheckIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'

import Status from '@/Components/Status.vue';
import Notification from '@/Components/Notification.vue';

const page = usePage()

const props = defineProps({
	auth: Object,
	navigation: Object,
	string_change: Object,
	orders: Array,
	order: Object,
})

const mobileMenuOpen = ref(false)
const orderOpen = props.order ? ref(true) : ref(false)

const pageNavigation = [
	{ name: 'Dashboard', icon: HomeIcon, href: route('dashboard'), current: false, visible: true },
	{ name: 'Pending', icon: TruckIcon, href: route('delivery.show'), current: true, visible: true },
	{ name: 'Delivery', icon: BriefcaseIcon, href: route('delivery.index'), current: false, visible: true },
]

function submit() {
	router.patch(route('delivery.status.update', props.order?.id));
}

const colorClasses = {
	pending: 'bg-yellow-100 text-yellow-900 rounded-full',
	accept: 'bg-blue-100 text-blue-900 rounded-full',
	collect: 'bg-rose-100 text-rose-900 rounded-full',
	delivered: 'bg-green-100 text-green-900 rounded-full',
};

const order_delivery = props.order.order_delivery;
</script>
<template>
	<Head title="Delivery" />

	<div class="flex h-screen print:hidden">
		<!-- Narrow sidebar -->
		<div class="hidden w-28 overflow-y-auto bg-primary-700 lg:block">
			<div class="flex w-full h-full flex-col items-center py-6">
				<a href="/" class="flex flex-shrink-0 items-center">
					<img v-if="page.props.app.logo" class="h-16 w-auto" :src="page.props.app.logo" alt="logo" />
					<span v-else class="text-secondary-500 px-3 text-xl font-fancy leading-6 font-medium"> {{ page.props.app.name }} </span>
				</a>
				<div class="mt-6 w-full flex-1 space-y-1 px-2">

					<Link v-for="item in pageNavigation" :key="item.name" v-show="item.visible" :href="item.href"
						:class="[item.current ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white', 'group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium']"
						:aria-current="item.current ? 'page' : undefined">
					<component :is="item.icon"
						:class="[item.current ? 'text-white' : 'text-primary-300 group-hover:text-white', 'h-6 w-6']"
						aria-hidden="true" />
					<span class="mt-2">{{ item.name }}</span>
					</Link>
				</div>

				<div class="pb-5 w-full flex flex-col space-y-1 px-2">
					<Link :href="route('logout')" method="post" as="button"
						:class="['text-primary-100 hover:bg-primary-800 hover:text-white', 'group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium']">
					<ArrowLeftOnRectangleIcon class="text-primary-300 group-hover:text-white h-6 w-6" aria-hidden="true" />
					<span class="mt-2 flex items-center">Logout</span>
					</Link>
					<Link :href="route('profile.index')" class="w-full flex-shrink-0">
					<img class="mx-auto block h-10 w-10 rounded-full" :src="auth.user?.image_url"
						alt="Image" />

					<div class="sr-only">
						<p>{{ auth.user?.name }}</p>
						<p>Account settings</p>
					</div>
					</Link>
				</div>

			</div>
		</div>

		<!-- Mobile menu -->
		<TransitionRoot as="template" :show="mobileMenuOpen">
			<Dialog as="div" class="relative z-20 lg:hidden" @close="mobileMenuOpen = false">
				<TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0"
					enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100"
					leave-to="opacity-0">
					<div class="fixed inset-0 bg-stone-600 bg-opacity-75" />
				</TransitionChild>

				<div class="fixed inset-0 z-40 flex">
					<TransitionChild as="template" enter="transition ease-in-out duration-300 transform"
						enter-from="-translate-x-full" enter-to="translate-x-0"
						leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0"
						leave-to="-translate-x-full">
						<DialogPanel class="relative flex w-full max-w-xs flex-1 flex-col bg-primary-700 pt-5 pb-4">
							<TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0"
								enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100"
								leave-to="opacity-0">
								<div class="absolute top-1 right-0 -mr-14 p-1">
									<button type="button"
										class="flex h-12 w-12 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-white"
										@click="mobileMenuOpen = false">
										<XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
										<span class="sr-only">Close sidebar</span>
									</button>
								</div>
							</TransitionChild>
							<div class="flex flex-shrink-0 items-center px-4">
								<img v-if="page.props.app.logo" class="h-16 w-auto" :src="page.props.app.logo" alt="logo" />
								<span v-else class="text-secondary-500 px-3 text-xl font-fancy leading-6 font-medium"> {{ page.props.app.name }} </span>
							</div>
							<div class="mt-5 h-0 flex-1 overflow-y-auto px-2">
								<nav class="flex h-full flex-col">
									<div class="space-y-1">
										<a v-for="item in pageNavigation" :key="item.name" v-show="item.visible"
											:href="item.href"
											:class="[item.current ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white', 'group py-2 px-3 rounded-md flex items-center text-sm font-medium']"
											:aria-current="item.current ? 'page' : undefined">
											<component :is="item.icon"
												:class="[item.current ? 'text-white' : 'text-primary-300 group-hover:text-white', 'mr-3 h-6 w-6']"
												aria-hidden="true" />
											<span>{{ item.name }}</span>
										</a>
									</div>
								</nav>
							</div>
						</DialogPanel>
					</TransitionChild>
					<div class="w-14 flex-shrink-0" aria-hidden="true">
						<!-- Dummy element to force sidebar to shrink to fit close icon -->
					</div>
				</div>
			</Dialog>
		</TransitionRoot>

		<Notification />

		<!-- Content area -->
		<div class="flex flex-1 flex-col overflow-hidden">
			<!-- Main content -->
			<div class="min-w-md flex flex-1 items-stretch overflow-hidden">

				<main class="flex-1 overflow-y-auto">
					<!-- Primary column -->
					<section aria-labelledby="primary-heading" class="flex h-full min-w-0 flex-1 flex-col lg:order-last">
						<h1 id="primary-heading" class="sr-only">Products</h1>
						<div class="flex flex-col relative w-full h-full divide-y divide-stone-200">

							<div class="flex-1 flex w-full shrink-0 bg-white space-y-2 overflow-y-auto">
								<section class="flex-1 flex flex-col lg:flex-row divide-x divide-stone-200 w-full">

									<div
										class="flex-1 flex flex-col relative z-0 divide-y divide-stone-200 overflow-hidden focus:outline-none">
										<header class="w-full">
											<div class="relative z-10 flex h-12 flex-shrink-0 bg-white shadow-sm">
												<button type="button"
													class="border-r border-stone-200 px-4 text-stone-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 lg:hidden"
													@click="mobileMenuOpen = true">
													<span class="sr-only">Open sidebar</span>
													<Bars3BottomLeftIcon class="h-6 w-6" aria-hidden="true" />
												</button>
												<div class="flex flex-1 justify-between px-4 sm:px-6">
													<div class="flex flex-1">
													</div>
												</div>
											</div>
										</header>

										<div class="flex-1 h-full overflow-y-auto py-2">
											<ul role="list" class="divide-y divide-gray-100">
												<li v-for="item in orders" :key="item.id"
													:class="[item.id == order.id ? 'bg-slate-50' : 'hover:bg-slate-50', 'relative']">
													<div class="px-4 sm:px-6 lg:px-8">
														<Link :href="route('delivery.show', item.id)"
															class="py-5 mx-auto flex max-w-4xl justify-between gap-x-6">
														<div class="flex gap-x-4">
															<div class="min-w-0 flex-auto">
																<div class="flex items-start gap-x-3">
																	<p class="text-sm font-medium leading-6 text-gray-700">
																		{{ item.order_delivery?.customer_name }}</p>
																	<Status class="ml-0" :name="item.order_delivery?.status"
																		:colors="colorClasses" />
																</div>
																<div
																	class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
																	<p class="whitespace-nowrap">
																		Order at <time :datetime="item.date">{{
																			item.datetime_format }}</time>
																	</p>
																</div>
																<div
																	class="mt-1 flex items-center gap-x-2 text-sm leading-5 font-medium text-gray-700">
																	<p class="">{{ item.order_delivery?.address }}</p>
																</div>
															</div>
														</div>
														<div class="flex items-center gap-x-4">
															<div class="hidden sm:flex sm:flex-col sm:items-end">
																<p class="text-base leading-6 text-gray-900">{{ item.total
																}} &#2547;</p>
																<!-- <p class="mt-1 text-xs leading-5 text-gray-500">
																		waiting <time :datetime="item.lastSeenDateTime">{{ item.order_delivery?.due_time }}</time>
																	</p> -->
															</div>
															<ChevronRightIcon class="h-5 w-5 flex-none text-gray-400"
																aria-hidden="true" />
														</div>
														</Link>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</section>
							</div>
						</div>
					</section>
				</main>

				<!-- Details sidebar -->
				<aside class="hidden w-96 bg-white lg:block relative">
					<!-- Mobile menu -->
					<TransitionRoot as="template" :show="orderOpen">
						<Dialog as="div" class="relative z-20" @close="orderOpen = false">
							<TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
								enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
								leave-to="opacity-0">
								<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
							</TransitionChild>

							<div class="fixed inset-0 overflow-hidden">
								<div class="absolute inset-0 overflow-hidden">
									<div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
										<TransitionChild as="template"
											enter="transform transition ease-in-out duration-500 sm:duration-700"
											enter-from="translate-x-full" enter-to="translate-x-0"
											leave="transform transition ease-in-out duration-500 sm:duration-700"
											leave-from="translate-x-0" leave-to="translate-x-full">
											<DialogPanel class="pointer-events-auto w-screen max-w-md">
												<form @submit.prevent="submit"
													class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
													<div class="flex items-start justify-between px-4 sm:px-6">
														<div
															class="text-lg font-medium text-gray-900 flex flex-col justify-center items-center w-full py-4">
															<div class="flex text-base font-medium space-x-0">
																<h3 v-if="order_delivery.customer_name != order_delivery.customer_mobile"
																	class="">{{ order_delivery.customer_name }}</h3>
																<svg v-if="order_delivery.customer_name != order_delivery.customer_mobile"
																	class="mt-0.5 shrink-0 h-5 w-5 text-gray-500"
																	xmlns="http://www.w3.org/2000/svg" fill="currentColor"
																	viewBox="0 0 20 20" aria-hidden="true">
																	<path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z">
																	</path>
																</svg>
																<a :href="'tel:' + order_delivery.customer_mobile"
																	class="">{{ order_delivery.customer_mobile }}</a>
															</div>

															<p class="mt-1 text-xs text-gray-500 whitespace-nowrap">
																Order at <time :datetime="order.date">{{
																	order.datetime_format }}</time>
															</p>
															<p class="mt-2 font-normal text-gray-700 text-sm">{{
																order_delivery.address }}</p>
														</div>
													</div>


													<div
														class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 border-t border-gray-200">
														<div class="mt-2">
															<div class="flow-root">
																<ul role="list"
																	class="-my-6 space-y-2 divide-y divide-gray-200">
																	<li v-for="(item, index) in order.products" :key="index"
																		class="flex flex-col sm:text-sm font-medium px-3 py-2 space-y-1">
																		<div
																			class="text-sm leading-5 font-semibold text-stone-800 capitalize">
																			{{ item.product_name }}
																		</div>
																		<div class="flex justify-center items-center">
																			<div
																				class="space-x-1 inline-flex text-sm leading-5 text-stone-600 text-right sm:text-left">
																				<span class="">{{ item.rate }}</span>
																				<span class=""> &#2547;</span>
																			</div>
																			<div
																				class="ml-auto space-x-1 flex rounded-md text-stone-600">
																				<span class="">Quantity</span>
																				<span class="">{{ item.quantity }}</span>
																			</div>
																		</div>
																	</li>

																</ul>
															</div>
														</div>
													</div>

													<div class="border-t border-gray-200 py-6 px-4 sm:px-6 space-y-2">
														<div v-if="order_delivery.accepted_at"
															class="flex justify-between text-sm font-normal text-gray-900">
															<p>Collection time</p>
															<p class="text-base font-normal">{{
																order_delivery.collection_time }}</p>
														</div>
														<div v-if="order_delivery.collected_at"
															class="flex justify-between text-sm font-normal text-gray-900">
															<p>Delivery time</p>
															<p class="text-base font-normal">{{ order_delivery.delivery_time
															}}</p>
														</div>
														<div class="flex justify-between text-sm font-normal text-gray-900">
															<p>Subtotal</p>
															<p class="text-base font-normal">{{ order.sub_total }} &#2547;
															</p>
														</div>
														<div class="flex justify-between text-sm font-normal text-gray-900">
															<p>VAT</p>
															<p class="text-base font-normal">{{ order.vat_amount }} &#2547;
															</p>
														</div>
														<div class="flex justify-between text-sm font-normal text-gray-900">
															<p>Delivery</p>
															<p class="text-base font-normal">{{ order.delivery_cost }}
																&#2547;</p>
														</div>
														<div class="flex justify-between text-sm font-medium text-gray-900">
															<p>Total</p>
															<p class="text-base font-semibold">{{ order.total }} &#2547;</p>
														</div>
														<div class="mt-4">
															<button v-if="!order.order_delivery?.accepted_at" type="submit"
																class="w-full inline-flex justify-center items-center gap-x-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-blue-50 shadow-sm hover:bg-blue-700 border border-blue-600">
																<CheckIcon class="hidden sm:block -ml-0.5 h-5 w-5"
																	aria-hidden="true" />
																Accept
															</button>
															<button v-else-if="!order.order_delivery?.collected_at"
																type="submit"
																class="w-full inline-flex justify-center items-center gap-x-1.5 rounded-md bg-rose-600 px-3 py-2 text-sm font-semibold text-rose-50 shadow-sm hover:bg-rose-700 border border-rose-600">
																Collect
															</button>
															<button v-else-if="!order.order_delivery?.delivered_at"
																type="submit"
																class="w-full inline-flex justify-center items-center gap-x-1.5 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-green-50 shadow-sm hover:bg-green-700 border border-green-600">
																Delivered
															</button>
														</div>
													</div>
												</form>
											</DialogPanel>
										</TransitionChild>
									</div>
								</div>
							</div>
						</Dialog>
					</TransitionRoot>

					<!-- <div>
						<div class="relative z-20">
							<div class="inset-0 overflow-hidden">
								<div class="absolute inset-0 overflow-hidden">
									<div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
										<div enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
											<div class="pointer-events-auto w-screen h-screen max-w-sm">
												<form @submit.prevent="submit" class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
													<div class="flex items-start justify-between px-4 sm:px-6">
														<div class="text-lg font-medium text-gray-900 flex flex-col justify-center items-center w-full py-4">
															<div class="flex text-base font-medium space-x-0">
																<h3 v-if="order_delivery.customer_name != order_delivery.customer_mobile" class="">{{ order_delivery.customer_name }}</h3>
																<svg v-if="order_delivery.customer_name != order_delivery.customer_mobile" class="mt-0.5 shrink-0 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"></path></svg>
																<a :href="'tel:' + order_delivery.customer_mobile" class="">{{ order_delivery.customer_mobile }}</a>
															</div>
															
															<p class="mt-1 text-xs text-gray-500 whitespace-nowrap">
																Order at <time :datetime="order.date">{{ order.datetime_format }}</time>
															</p>
															<p class="mt-2 font-normal text-gray-700 text-sm">{{ order_delivery.address }}</p>
														</div>
													</div>

													<div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 border-t border-gray-200">
														<div class="mt-2">
															<div class="flow-root">
																<ul role="list" class="-my-6 space-y-2 divide-y divide-gray-200">
																	<li v-for="(item, index) in order.products" :key="index" class="flex flex-col sm:text-sm font-medium px-3 py-2 space-y-1">
																		<div class="text-sm leading-5 font-semibold text-stone-800 capitalize">
																			{{ item.product_name }}
																		</div>
																		<div class="flex justify-center items-center">
																			<div class="space-x-1 inline-flex text-sm leading-5 text-stone-600 text-right sm:text-left">
																				<span class="">{{ item.rate }}</span>
																				<span class=""> &#2547;</span>
																			</div>
																			<div class="ml-auto space-x-1 flex rounded-md text-stone-600">
																				<span class="">Quantity</span>
																				<span class="">{{ item.quantity }}</span>
																			</div>
																		</div>
																	</li>

																</ul>
															</div>
														</div>
													</div>

													<div class="border-t border-gray-200 py-6 px-4 sm:px-6 space-y-2">
														<div v-if="order_delivery.accepted_at" class="flex justify-between text-sm font-normal text-gray-900">
															<p>Collection time</p>
															<p class="text-base font-normal">{{ order_delivery.collection_time }}</p>
														</div>
														<div v-if="order_delivery.collected_at" class="flex justify-between text-sm font-normal text-gray-900">
															<p>Delivery time</p>
															<p class="text-base font-normal">{{ order_delivery.delivery_time }}</p>
														</div>
														<div class="flex justify-between text-sm font-normal text-gray-900">
															<p>Subtotal</p>
															<p class="text-base font-normal">{{ order.sub_total }} &#2547;</p>
														</div>
														<div class="flex justify-between text-sm font-normal text-gray-900">
															<p>VAT</p>
															<p class="text-base font-normal">{{ order.vat_amount }} &#2547;</p>
														</div>
														<div class="flex justify-between text-sm font-normal text-gray-900">
															<p>Delivery</p>
															<p class="text-base font-normal">{{ order.delivery_cost }} &#2547;</p>
														</div>
														<div class="flex justify-between text-sm font-medium text-gray-900">
															<p>Total</p>
															<p class="text-base font-semibold">{{ order.total }} &#2547;</p>
														</div>
														<div class="mt-4">
															<button v-if="!order.order_delivery?.accepted_at" type="submit" class="w-full inline-flex justify-center items-center gap-x-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-blue-50 shadow-sm hover:bg-blue-700 border border-blue-600">
																<CheckIcon class="hidden sm:block -ml-0.5 h-5 w-5" aria-hidden="true" />
																Accept
															</button>
															<button v-else-if="!order.order_delivery?.collected_at" type="submit" class="w-full inline-flex justify-center items-center gap-x-1.5 rounded-md bg-rose-600 px-3 py-2 text-sm font-semibold text-rose-50 shadow-sm hover:bg-rose-700 border border-rose-600">
																Collect
															</button>
															<button v-else-if="!order.order_delivery?.delivered_at" type="submit" class="w-full inline-flex justify-center items-center gap-x-1.5 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-green-50 shadow-sm hover:bg-green-700 border border-green-600">
																Delivered
															</button>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> -->

			</aside>

		</div>
	</div>
</div></template>