const app = Vue.createApp({
    data() {
        return {
            sortParam: 'date',
            server: 9,
            servers: {
                1: 'Фанем',
                2: 'Нуи',
                3: 'Шаеда',
                4: 'Корвус',
                5: 'Луций',
                6: 'Каиль',
                7: 'Ария',
                8: 'Хазе',
                9: 'Сервер ?',
                10: 'Ренессанс',
                11: 'Кракен',
            },
            startServer: true,
            startChecks: true,
            pUserId: 0,
            userId: 0,
            uself: false,
            puserData: {},
            prices: [],
            checked: [],
            isFolow: false

        }
    },

    watch: {
        server: {
            handler(val){
                if(!this.startServer){
                    this.setServer()
                }else{
                    this.startServer = false
                }
            }
        },

        checked: {
            handler(val){
                if(!this.startChecks){
                    this.saveChecks()
                }else{
                    this.startChecks = false
                }

            },
            deep: true
        },


    },

    methods: {

        setServer() {
            $.ajax
            ("hendlers/set_server.php",
                {
                    type: "POST",
                    data: {
                        server: this.server
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                //pt.reloadPrices()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        getPuser() {
            $.ajax
            ("hendlers/user/get.php",
                {
                    type: "POST",
                    data: {
                        puser: this.pUserId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.puserData = data
                pt.isFolow = data.isFolow

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        getPrices() {
            $.ajax
            ("hendlers/user/get_prices.php",
                {
                    type: "POST",
                    data: {
                        puser: this.pUserId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.prices = data.prices
                pt.checked = data.checked

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        saveChecks() {
            if (!this.uself) {
                return
            }

            $.ajax
            ("hendlers/items/isbuys.php",
                {
                    type: "POST",
                    data: {
                        checked: this.checked
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'


            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })

        },

        savePrice(id, price) {
            if (!this.uself) {
                return
            }

            price = '' + price
            price = price.replace(/[^\d]/g, '')

            $.ajax
            ("hendlers/price/set_price.php",
                {
                    type: "POST",
                    data: {
                        item_id: id,
                        price: price
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'


            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })

        },

        setPrice(idx, price) {

            if (!this.uself) {
                return
            }

            price = '' + price
            price = price.replace(/^0+/, '');
            price = price.replace(/[^\d]/g, '')

            price = price * 1
            this.prices[idx].price = price

            this.savePrice(this.prices[idx].item_id, price)

            price = this.priceStringer(price)

            return price
        },

        priceStringer(str) {
            str = str + ''
            str = str.replace(/[^\d]/g, '')
            if (str == 0) {
                str = 0
            }

            let len = str.length;
            if (len > 2) {
                str = str.substring(0, len - 2) + " " + str.substring(len - 2);
                len = str.length;
            }
            if (len > 5) {
                str = str.substring(0, len - 5) + " " + str.substring(len - 5);
            }

            return str
        },

        delPrice(itemId) {
            $.ajax
            ("hendlers/price/del_price.php",
                {
                    type: "POST",
                    data: {
                        item_id: itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.getPrices()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
                //  console.log(pt.error)
            })
        },

        sortByDate(d1, d2) {
            return (d1.timestamp < d2.timestamp) ? 1 : -1;

        },

        sortByName(d1, d2) {
            return (d1.item_name.toLowerCase() > d2.item_name.toLowerCase()) ? 1 : -1;
        },

        valutImager(value, vid = 500) {
            return valutImager(value, vid)
        },

        copy(val) {
            try {
                navigator.clipboard.writeText(val)
            } catch (e) {
                throw e
            }
        },

        goToItem(item) {
            goToItem(item)
        },
    },

    computed: {

        errorMsg() {
            switch (this.error) {
                case 'ok':
                    return 'ok';
                case 'no data':
                    return 'Ничего не вижу';
                case 'user':
                    return 'Ошибка авторизации';
                case 'dbError':
                    return 'Ошибка записи в базу данных';
                default:
                    return 'Ничего не понимаю';
            }
        },

        sortedPrices() {
            switch (this.sortParam) {
                case 'date':
                    return this.prices.sort(this.sortByDate);
                case 'name':
                    return this.prices.sort(this.sortByName);

                default:
                    return this.prices;
            }
        },

    },

    mounted() {
        let userver = document.getElementById("server").value;
        if(userver){
            this.server = userver
        }

        this.userId = document.getElementById("userId").value;
        this.pUserId = document.getElementById("pUserId").value;
        this.uself = (this.userId == this.pUserId)
        this.getPuser()
        this.getPrices()
    }

})

const pt = app.mount('#rent')