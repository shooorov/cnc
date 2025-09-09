import { defineStore } from 'pinia'

export const useOrderStore = defineStore('order', {
    state: () => ({
        address: null,
        locationId: null,
        branchId: null,
        note: null,
        mobile: null,
        subTotal: 0,
        total: 0,

        dineType: 'delivery',
        discountType: 'percent',
        discountRate: 0,
        discountAmount: 0,
        vatAbleAmount: 0,
        vatAmount: 0,

        deliveryCost: 0,
        serviceCost: 0,
        vatRate: 0,

        groupItems: [],
        categories: []
    }),

    getters: {
        exists: (state) => state.groupItems.length > 0,
        itemCount: (state) => state.groupItems.reduce((carry, val) => carry + Number(val.quantity), 0)
    },
	
    actions: {
        updateQuantity() {
            this.categories.map((category) => {
                category.products.map((product) => {
                    product.quantity = this.groupItems.find((i) => i.productId == product.id)?.quantity ?? 0
                    return product
                })
                return category
            })
        },

        filterAmount(amount) {
            return Math.round(amount)
        },

        getProduct(productId) {
            return this.categories
                .map((category) => {
                    return category.products.find((product) => product.id == productId)
                })
                .filter((category) => category?.id)[0]
        },

        addMenuItem(productId) {
            let groupItem = this.groupItems.find((i) => i.productId == productId)

            if (groupItem) {
                groupItem.quantity++
            } else {
                let product = this.getProduct(productId)

                if (!product.rate) {
                    alert('Item price not set yet!')
                    return
                }

                this.groupItems.push({
                    id: '',
                    status: 'pending',
                    productId: product.id,
                    productName: product.name,
                    rate: product.rate,
                    code: product.code,
                    vatApplicable: product.vat_applicable,
                    quantity: 1
                })
            }
            this.updateQuantity()
            this.calculation()
        },

        subMenuItem(productId) {
            let groupItem = this.groupItems.find((i) => i.productId == productId)
            let groupItemIndex = this.groupItems.findIndex((i) => i.productId == productId)

            if (groupItem.quantity == 0) {
                return
            }

            groupItem.quantity--

            if (groupItem.quantity == 0) {
                this.groupItems.splice(groupItemIndex, 1)
            }
            this.updateQuantity()
            this.calculation()
        },

        calculation() {
            let discountRate = Number(this.discountRate)
            let vatRate = Number(this.vatRate)
            let cashAmount = Number(this.cash)
            let groupItems = this.groupItems

            let subTotal = groupItems.reduce((carry, val) => carry + Number(val.rate) * Number(val.quantity), 0) ?? 0

            let nonVatAbleAmount = groupItems.filter((i) => !i.vatApplicable).reduce((carry, val) => carry + Number(val.rate) * Number(val.quantity), 0) ?? 0

            let discountPercent = discountRate / 100
            let discountAmount = this.filterAmount(this.discountType == 'percent' ? subTotal * discountPercent : discountRate)

            let fractionDiscount = this.filterAmount(nonVatAbleAmount > 0 ? (nonVatAbleAmount / subTotal ?? 0) * discountAmount : 0)

            let newNonVatAbleAmount = nonVatAbleAmount - fractionDiscount

            // For service cost added if any
            let serviceCost = Number(this.serviceCost)
            let vatAbleAmount = subTotal - discountAmount - newNonVatAbleAmount + serviceCost

            // For no service cost added if any
            // let vatAbleAmount = subTotal - discountAmount - newNonVatAbleAmount;

            let vatPercent = vatRate / 100
            let vatAmount = this.filterAmount(vatAbleAmount * vatPercent)

            // For service cost added if any
            let total = subTotal - discountAmount + vatAmount + serviceCost

            // For no service cost added if any
            // let total = subTotal - discountAmount + vatAmount;

            let changeAmount = cashAmount > 0 ? cashAmount - total : 0

            this.subTotal = subTotal.toFixed(2)
            this.discountAmount = discountAmount.toFixed(2)
            this.vatAbleAmount = vatAbleAmount.toFixed(2)
            this.serviceCost = serviceCost.toFixed(2)
            this.vatAmount = vatAmount
            this.total = total.toFixed(2)
            this.change = changeAmount.toFixed(2)
        }
    }
})
