import { reactive } from 'vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const storage = reactive({
	refreshDuration: 15,
	executionTime: null,
	categories: [],
	customers: [],
	dineTypes: [],
	discountTypes: [],
	employees: [],
	paymentMethods: [],
	products: [],
	riders: [],
	statuses: [],
	topProducts: [],

	async get() {
		// console.log("get");
		let record = JSON.parse(sessionStorage.getItem(appName + '/pos'))
		// console.log("executionTime: " + record?.executionTime);

		let diffSeconds = (new Date().getTime() - (record?.executionTime ?? 0)) / 1000;

		// console.log("diffSeconds: " + diffSeconds);

		if(!record || diffSeconds >  this.refreshDuration) {
			await axios.get('/api/basic')
				.then((response) => {
					this.executionTime = new Date().getTime()
					record = response.data
					
					console.log("record load from server");
					// console.log(record);
				}).catch((error) => {
					console.log(error);
				})
		}else {
			console.log("record load from storage");
			// console.log(record);
			this.executionTime = record.executionTime
		}

		this.categories = record.categories
		this.customers = record.customers
		this.dineTypes = record.dineTypes
		this.discountTypes = record.discountTypes
		this.employees = record.employees
		this.paymentMethods = record.paymentMethods
		this.products = record.products
		this.riders = record.riders
		this.statuses = record.statuses
		this.topProducts = record.topProducts
		this.set();
	},
	set() {
		sessionStorage.setItem(appName + '/pos', JSON.stringify(this))
		// console.log("set");
	}
})

export { storage }