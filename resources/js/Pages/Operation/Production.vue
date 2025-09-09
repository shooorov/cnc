<script setup>
import { reactive, ref, onMounted, onUpdated, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'

import KitchenLayout from '@/Layouts/KitchenLayout.vue'
import Notification from '@/Components/Notification.vue'

import sound from '@/../audio/facility-alarm-908.wav'
import { BellAlertIcon } from '@heroicons/vue/24/solid'
import { HashtagIcon } from '@heroicons/vue/20/solid'

const page = usePage()

const props = defineProps({
    auth: Object,
    productions: Array,
    order_productions: Array
})

const form = useForm({
    production_id: '',
    status: ''
})

const clock = reactive({
    isAlarming: false,
    seconds: null,
    time: null
})

const selected = ref(null)
const clockInterval = ref(null)
const alarmInterval = ref(null)
const isCook = ref(props.auth.user.is_cook)
const isWaiter = ref(props.auth.user.is_waiter)
const isClocking = ref(page.props.kitchen ? true : false)

const playAlarmSound = () => {
    if (alarmInterval.value) {
        clearInterval(alarmInterval.value)
    }

    alarmInterval.value = setInterval(function () {
        let hour = new Date().getHours()
        let min = new Date().getMinutes()
        let sec = new Date().getSeconds()

        clock.time = hour + ':' + min + ':' + sec
        clock.seconds = sec
        clock.isAlarming = [0, 1, 2].includes(sec)
        // clock.isAlarming = true

        if (isCook.value && clock.seconds == 0 && props.productions.find((i) => i.status == 'pending' && !i.is_viewed)) {
              new Audio(sound).play()
        }

        if (isWaiter.value && clock.seconds == 0 && props.productions.find((i) => i.waiter_alert)) {
              new Audio(sound).play()
        }
    }, 1000)
}

onMounted(() => {
    jobTimeCounting()

    playAlarmSound()
})

onUpdated(() => {
    jobTimeCounting()

    playAlarmSound()
})

watch(
    () => selected.value,
    (newVal) => {
        form.production_id = newVal?.id

        if (isCook.value && !newVal.is_viewed) {
			page.props.alertMessage = {};

			form.post(route('production.view'), {
                onSuccess: () => {},
                onFinish: () => {
                    form.reset()
                }
            })
        }
    }
)

const touch = (production) => {
	console.log(production);
    selected.value = production
}

const isOrderWaiting = (production) => {
	console.log("isOrderWaiting", production);
    return (isCook.value && !production.is_viewed) || (isWaiter.value && production.waiter_alert)
}

const isOrderChangeable = (production) => {
	console.log("isOrderChangeable", production);
	console.log(production.id, form.production_id);
	console.log(isCook.value, production.is_viewed, isClocking.value, production.status != 'complete');
    return (
        production.id == form.production_id &&
        ((isWaiter.value && production.waiter_alert) || (isCook.value && production.is_viewed && isClocking.value && production.status != 'complete'))
    )
}

const nextStatus = () => {
    form.status = 'next'
    if (isCook.value) {
		page.props.alertMessage = {};

		form.post(route('production.status'), {
            onSuccess: () => {},
            onFinish: () => {
                form.reset()
            }
        })
    } else if (isWaiter.value) {
		page.props.alertMessage = {};

		form.post(route('production.delivered'), {
            onSuccess: () => {},
            onFinish: () => {
                form.reset()
            }
        })
    } else {
        alert('Only Waiter and chef / barista can change production status')
    }
}

function getPlainTime(datetime) {
    return new Date(datetime).getTime()

    // let ref = new Date(datetime);
    // let plain_time = ref.getFullYear() + "-" + (ref.getMonth() + 1) + "-" + ref.getDate() + " " + ref.getHours() + ":" + ref.getMinutes() + ":00";

    // return new Date(plain_time).getTime();
}

function jobTimeCounting() {
    if (clockInterval.value) {
        clearInterval(clockInterval.value)
    }

    let productions = props.productions
    let order_productions = props.order_productions

    clockInterval.value = setInterval(function () {
        productions.forEach((production) => {
            // Get today's date and time
            var now = new Date().getTime()
            // Find the distance between now and the count up date

            var distance = now - getPlainTime(production.created_at)
            distance = production.accepted_at ? now - getPlainTime(production.accepted_at) : distance
            distance = production.completed_at ? now - getPlainTime(production.completed_at) : distance

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24))
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
            var seconds = Math.floor((distance % (1000 * 60)) / 1000)

            let seconds_time = seconds ? seconds + ' Secs' : ''
            let minutes_time = minutes ? minutes + ' Mins ' : ''
            let hours_time = hours ? hours + ' Hrs ' : ''
            let days_time = days ? days + ' Days ' : ''
            if (minutes != '' || hours != '') {
                production.calculated_time = hours_time + minutes_time
            } else {
                production.calculated_time = seconds_time
            }
        })
        order_productions.forEach((production) => {
            // Get today's date and time
            var now = new Date().getTime()
            // Find the distance between now and the count up date

            var distance = now - getPlainTime(production.created_at)
            distance = production.accepted_at ? now - getPlainTime(production.accepted_at) : distance
            distance = production.completed_at ? now - getPlainTime(production.completed_at) : distance

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24))
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
            var seconds = Math.floor((distance % (1000 * 60)) / 1000)

            let seconds_time = seconds ? seconds + ' Secs' : ''
            let minutes_time = minutes ? minutes + ' Mins ' : ''
            let hours_time = hours ? hours + ' Hrs ' : ''
            let days_time = days ? days + ' Days ' : ''
            if (minutes != '' || hours != '') {
                production.calculated_time = hours_time + minutes_time
            } else {
                production.calculated_time = seconds_time
            }
        })
    }, 1000)
}

const colors = {
    pending: {
        notifyText: 'Waiting',
        notifyClass: 'text-yellow-800',
        switchClass: 'bg-yellow-200 text-stone-800',
        cardClassNotViewed: 'bg-yellow-100 shadow-stone-300',
        cardClass: 'bg-green-100 shadow-green-700',
        buttonText: 'Accept',
        buttonClass: 'bg-green-600 hover:bg-green-700 text-white text-lg shadow-green-700 focus:ring-green-600'
    },

    accept: {
        notifyText: 'Cooking',
        notifyClass: 'text-red-500',
        switchClass: 'bg-green-300 text-stone-800',
        cardClass: 'bg-blue-100 shadow-stone-300',
        buttonText: 'Set Done',
        buttonClass: 'bg-blue-600 hover:bg-blue-700 text-white text-lg shadow-blue-700 focus:ring-blue-600'
    },

    complete: {
        notifyText: 'Finished',
        notifyClass: 'text-neutral-800',
        switchClass: 'bg-neutral-100 text-neutral-800',
        cardClass: 'bg-neutral-100 shadow-stone-300',
        buttonText: 'Delivered',
        buttonClass: 'bg-yellow-600 hover:bg-yellow-700 text-white text-lg shadow-yellow-700 focus:ring-yellow-600'
    }
}
</script>
<template>
    <Head title="Kitchen" />

    <KitchenLayout>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <Notification />

            <div class="px-4 sm:px-0">
                <div class="px-2 flex-1 flex flex-col w-full shrink-0 bg-white overflow-x-auto">
                    <section class="flex pb-32 gap-5">
                        <div v-show="order_productions.length > 0" class="flex border-2 border-blue-500 rounded-lg gap-5">
                            <div v-for="production in order_productions" :key="production.id" class="relative focus:outline-none">
                                <div class="relative w-64 lg:w-80 flex-1 flex flex-col text-sm text-slate-900">
                                    <div
                                        v-show="isOrderWaiting(production)"
                                        class="absolute mt-2 mr-2 right-0 flex justify-center items-center bg-red-600 w-10 h-10 rounded-full">
                                        <BellAlertIcon :class="[clock.isAlarming ? 'animate-wiggle' : '', 'w-7 h-7 text-yellow-100']" />
                                    </div>
                                    <div
                                        @click="touch(production)"
                                        :class="[
                                            'px-4 py-4 rounded-lg shadow-slate-400/50',
                                            production.id == form.production_id ? 'shadow-lg' : 'shadow',
                                            production.is_viewed ? colors[production.status].cardClass : colors[production.status].cardClassNotViewed
                                        ]">
                                        <h2 class="flex font-semibold text-lg leading-5 text-slate-700">
                                            Order
                                            <HashtagIcon class="ml-2 h-5 w-5" aria-hidden="true" />
                                            {{ production.order_number.substr(6) }}
                                        </h2>
                                        <p class="mt-2 leading-5 text-slate-700">Ref: {{ production.stuff_name }}</p>

                                        <ul v-if="production" class="flex-1 flex flex-col overflow-y-auto py-6 h-48 lg:h-72">
                                            <li v-for="(item, index) in production?.items.data ?? []" v-show="item.quantity != 0" :key="index">
                                                <div class="flex items-start py-1 space-x-3">
                                                    <div
                                                        class="w-6 h-6 flex items-center justify-center capitalize rounded-full border border-slate-700 text-sm">
                                                        {{ parseInt(index) + 1 }}
                                                    </div>
                                                    <div class="flex-1 capitalize font-medium text-sm lg:text-base">
                                                        {{ item.product_name }}
                                                    </div>
                                                    <div
                                                        class="relative h-6 flex items-center justify-start border border-l-0 rounded-full bg-lime-500 border-slate-700 font-medium text-lime-900 space-x-1">
                                                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-white border border-slate-700">
                                                            <span class="">{{ item.quantity }}</span>
                                                        </div>
                                                        <span class="pr-1.5 text-xs">pcs</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>

                                        <ul v-else class="flex-1 flex flex-col overflow-y-auto py-6 h-72">
                                            <li class="flex items-center border-t border-slate-400/20 py-3">
                                                <p class="text-sm text-center font-medium text-red-800 bg-red-50 w-full px-4 py-2">Not found</p>
                                            </li>
                                        </ul>

										<div class="flex flex-col space-y-1">
											<label class="text-xs font-semibold uppercase">Note :</label>
											<p class="py-1 px-2 h-8 flex items-start border border-dashed border-slate-700 leading-5 text-slate-700 ">{{ production.order_note }}</p>
                                        </div>
                                    </div>

                                    <div class="absolute bottom-0 w-full -mb-28 px-3 py-3 h-32 flex flex-col justify-center items-center">
                                        <button
                                            @click="nextStatus"
                                            v-show="isOrderChangeable(production)"
                                            type="button"
                                            :class="[
                                                colors[production.status].buttonClass,
                                                'inline-flex items-center px-10 py-1.5 border border-transparent shadow-md text-lg uppercase tracking-widest font-medium rounded text-white focus:outline-none focus:ring-2 focus:ring-offset-2'
                                            ]">
                                            {{ colors[production.status].buttonText }}
                                        </button>

                                        <p class="mt-4 text-base font-medium">
                                            Order time:
                                            <span>{{ production.created_at_format }}</span>
                                        </p>
                                        <p v-show="production.completed_at" class="text-base font-medium">
                                            Duration: <span>{{ production.time_duration }}</span>
                                        </p>

                                        <h3 v-if="production.delivered_at" class="text-lg font-medium text-slate-800">Delivered</h3>
                                        <h3 v-else :class="['text-lg font-medium', colors[production.status].notifyClass]">
                                            {{ production.calculated_time }}
                                            <span class="">{{ colors[production.status].notifyText }}</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-show="productions.length > 0" class="flex border-2 border-transparent rounded-lg gap-5">
                            <div v-for="production in productions" :key="production.id" class="relative focus:outline-none">
                                <div class="relative w-64 lg:w-80 flex-1 flex flex-col text-sm text-slate-900">
                                    <div
                                        v-show="isOrderWaiting(production)"
                                        class="absolute mt-2 mr-2 right-0 flex justify-center items-center bg-red-600 w-10 h-10 rounded-full">
                                        <BellAlertIcon :class="[clock.isAlarming ? 'animate-wiggle' : '', 'w-7 h-7 text-yellow-100']" />
                                    </div>
                                    <div
                                        @click="touch(production)"
                                        :class="[
                                            'px-4 py-4 rounded-lg shadow-slate-400/50',
                                            production.id == form.production_id ? 'shadow-lg' : 'shadow',
                                            production.is_viewed ? colors[production.status].cardClass : colors[production.status].cardClassNotViewed
                                        ]">
                                        <h2 class="flex font-semibold text-lg leading-5 text-slate-700">
                                            Order
                                            <HashtagIcon class="ml-2 h-5 w-5" aria-hidden="true" />
                                            {{ production.order_number.substr(6) }}
                                        </h2>
                                        <p class="mt-2 leading-5 text-slate-700">Ref: {{ production.stuff_name }}</p>

                                        <ul v-if="production" class="flex-1 flex flex-col overflow-y-auto py-6 h-48 lg:h-72">
                                            <li v-for="(item, index) in production.items.data" v-show="item.quantity != 0" :key="index">
                                                <div class="flex items-start py-1 space-x-3">
                                                    <div
                                                        class="w-6 h-6 flex items-center justify-center capitalize rounded-full border border-slate-700 text-sm">
                                                        {{ parseInt(index) + 1 }}
                                                    </div>
                                                    <div class="flex-1 capitalize font-medium text-sm lg:text-base">
                                                        {{ item.product_name }}
                                                    </div>
                                                    <div
                                                        class="relative h-6 flex items-center justify-start border border-l-0 rounded-full bg-lime-500 border-slate-700 font-medium text-lime-900 space-x-1">
                                                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-white border border-slate-700">
                                                            <span class="">{{ item.quantity }}</span>
                                                        </div>
                                                        <span class="pr-1.5 text-xs">pcs</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>

                                        <ul v-else class="flex-1 flex flex-col overflow-y-auto py-6 h-72">
                                            <li class="flex items-center border-t border-slate-400/20 py-3">
                                                <p class="text-sm text-center font-medium text-red-800 bg-red-50 w-full px-4 py-2">Not found</p>
                                            </li>
                                        </ul>

                                        <div class="flex flex-col space-y-1">
											<label class="text-xs font-semibold uppercase">Note :</label>
											<p class="py-1 px-2 h-8 flex items-start border border-dashed border-slate-700 leading-5 text-slate-700 ">{{ production.order_note }}</p>
                                        </div>
                                    </div>

                                    <div class="absolute bottom-0 w-full -mb-28 px-3 py-3 h-32 flex flex-col justify-center items-center">
                                        <button
                                            type="button"
                                            @click="nextStatus"
                                            v-show="isOrderChangeable(production)"
                                            :class="[
                                                colors[production.status].buttonClass,
                                                'inline-flex items-center px-10 py-1.5 border border-transparent shadow-md text-lg uppercase tracking-widest font-medium rounded text-white focus:outline-none focus:ring-2 focus:ring-offset-2'
                                            ]">
                                            {{ colors[production.status].buttonText }}
                                        </button>

                                        <p class="mt-4 text-base font-medium">
                                            Order time:
                                            <span>{{ production.created_at_format }}</span>
                                        </p>
                                        <p v-show="production.completed_at" class="text-base font-medium">
                                            Duration: <span>{{ production.time_duration }}</span>
                                        </p>

                                        <h3 v-if="production.delivered_at" class="text-lg font-medium text-slate-800">Delivered</h3>
                                        <h3 v-else :class="['text-lg font-medium', colors[production.status].notifyClass]">
                                            {{ production.calculated_time }}
                                            <span class="">{{ colors[production.status].notifyText }}</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-show="productions.length == 0 && order_productions.length == 0" class="w-full">
                            <div class="flex items-center py-3">
                                <p class="text-base text-center font-medium text-red-800 bg-red-50 w-full px-4 py-2">Production order empty. No order found!</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </KitchenLayout>
</template>
