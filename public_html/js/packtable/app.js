const sortByZoneFrom = (d1, d2) => (d1.Pack.z_from_name.toLowerCase() > d2.Pack.z_from_name.toLowerCase()) ? 1 : -1;
const sortByZoneTo = (d1, d2) => (d1.Pack.z_to_name.toLowerCase() > d2.Pack.z_to_name.toLowerCase()) ? 1 : -1;
const sortByProfitOr = (d1, d2) =>(d1.profitor < d2.profitor) ? 1 : -1;
const sortByProfit = (d1, d2) =>(d1.profit < d2.profit) ? 1 : -1;
const sortBySalary = (d1, d2) =>(d1.goldsalary < d2.goldsalary) ? 1 : -1;

const pt = Vue.createApp( {
    data() {
        return {
            sortParam: 'profit',
            packData: [],
            lost: [],
            packForm: {
                per: 130,
                pack_age: 0,
                condition: 1,
                siol: 0,
                type: [],
                filterFrom: 0
            },
            side: 0,
            zonesFrom: {
                0: {}
            },
            zonesTo:{
                0:{}
            },
            zFrom: 0,
            zTo: 0,
            zidx: 0,
            uPrices: {},
            error: 'ok',
            color: 'gray',
            min: 0,
            tiptops: [{tip_text:''}],
            tiptop: '',
            all: false,
            loaded: false,
            zoneFilter: {},
            setup: false

        }
    },

    watch: {


        zFrom: {
            handler(val){
                this.refreshZones()

            },
        },

        zonesTo: {
            handler(val){

                if (typeof this.zonesTo[this.zTo] === "undefined") {
                    this.zTo = 0
                }
            },
            deep: true
        },

        packForm:{
            handler(val){
                this.getPacks()
                this.newTiptop()
            },
            deep: true
        },

        'packForm.per'(){
            let per = toNums(this.packForm.per)
            localStorage.setItem ("packPer",per);
        },

        side: {
            handler(val){
                this.zTo = 0
                this.zFrom = 0

                this.getPacks()
                this.newTiptop()
            }
        },

        uPrices:{
            handler(val){
                var i = val.length;
                while (i--){
                    pt.uPriceValidator(i,val[i].gold,val[i].silver,val[i].bronze)
                }
            },
            deep: true
        }
    },

    methods: {

        refreshZones() {
            let zonesFrom = this.zonesFrom[this.sside()].filter(obj => {
                return obj.zone_id === this.zFrom
            })
            if (typeof zonesFrom[0] === "undefined") {
                this.zonesTo = {}
                return
            }
            this.zonesTo = zonesFrom[0].zonesTo
            //console.log(res)
        },

        sside() {
            if(this.side > 0)
                return this.side

            return 1
        },

        newTiptop() {
            if(this.error === 'ok')
                this.tiptop = newTiptop(this.tiptops)
        },

        getTipTops(){
            getTipTops().done(function (data) {
                pt.error = 'ok'
                pt.tiptops = data
                pt.newTiptop()
            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        sendPrices() {

            $.ajax
            ("hendlers/setpricelist.php",
                {
                    type: "POST",
                    data: { prices: this.uPrices},
                    dataType: "json",
                    cache: false

                }).done(function (data) {
                pt.error = 'ok'

                pt.getPacks()
            }).fail(function (data) {

                pt.error = data.responseText ?? 'yy'
                $('#tiptop').html(pt.errorMsg)
            })
        },

        uPriceValidator(i,gol,sil,bro){
            if(gol < 0){ gol = 0; pt.uPrices[i].gold = 0}
            if(sil < 0){ sil = 0; pt.uPrices[i].silver = 0 }
            if(bro < 0){ sil = 0; pt.uPrices[i].bronze = 0 }

            if(bro > 99){bro = 99}
            if(sil > 99){sil = 99}
            if(gol > 99999999){gol = 99999999}

            let sum = gol*10000 + sil*100 + bro
            if(sum < 0){
                sum = 0;
            }
            pt.uPrices[i].sum = sum;
        },

        cleanUPrice(idx) {
            pt.uPrices[idx].sum = 0;
            pt.uPrices[idx].gold = null
            pt.uPrices[idx].silver = null
            pt.uPrices[idx].bronze = null
        },

        getPacks(){
            if(!this.setup) return

            pt.lost = []
            pt.uPrices = []
            localStorage.setItem ("packTypes", JSON.stringify(pt.packForm.type));
            if(!this.side){
                this.error = 'side'
                this.tiptop = this.errorMsg
                return false
            }
            let data = {}
            Object.assign(data, this.packForm)
            data.side = this.side

            this.jdunOn()
            $.ajax
            ({
                url: "hendlers/packs_list.php",
                type: "POST",
                data: data,
                dataType: "json",
                cache: false

            }).done(function(data) {
                pt.error = 'ok'
                pt.jdunOff()
                if(typeof data.lost!== 'undefined'){
                    pt.lost = data.lost
                    var i = pt.lost.length;

                    while (i--){
                        var toarr = {
                            item_id: pt.lost[i].item_id,
                            item_name: pt.lost[i].item_name,
                            icon:  pt.lost[i].icon,
                            gold: null,
                            silver: null,
                            bronze: null,
                            sum: 0,
                            color: '#f35454'
                        }

                        pt.uPrices.push(toarr)
                    }

                    //console.log(pt.lost)
                   pt.packData = []
                }else{
                    pt.packData = data
                    pt.lost = []
                }
                pt.refreshZones()



            }).fail(function(data) {
                pt.jdunOff()

                pt.packData = []
                pt.lost = []
                pt.error = data.responseText ?? 'yy'
                pt.tiptop = pt.errorMsg
            })

        },

        setZones(side){
            getZones(side).done(function (data) {
                    pt.error = 'ok'
                    pt.zonesFrom = data
                    pt.zonesTo = pt.zonesFrom[pt.sside()][0].zonesTo
                    pt.loaded = true
                    //loaded.sortZones()
                }
            ).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        valutImager(value,vid = 500){
            return valutImager(value,vid);
        },

        jdunOn() {
            let jdun = $("#jdun");
            jdun.removeClass("jdun"); jdun.addClass("loading");
        },

        jdunOff() {
            let jdun = $("#jdun");
            jdun.removeClass("loading"); jdun.addClass("jdun");
            this.newTiptop()
        },

        getParams() {
            let per = localStorage.getItem('packPer') ?? 130;
            per = perValid(per);
            this.packForm.per = per;

            var ls = localStorage.getItem('packTypes') ?? JSON.stringify([]);
            ls = JSON.parse(ls)
            this.packForm.type = ls
            localStorage.setItem("packTypes", JSON.stringify([]))

            this.setup = true
        },

        goToItem(item){

            item = toNums(item)
            if(item){
                localStorage.setItem ('item',item);
                document.location.href = "catalog.php";
            }

        },

        sortZones(){
            if(!Object.keys(this.zonesFrom).length)
                return {}

            this.zonesFrom[1] = Object.entries(this.zonesFrom[1])/*.sort(sortByProp('zone_name'));*/
            this.zonesFrom[2] = [].slice.call(this.zonesFrom[2]).sort(sortByProp('zone_name'));
            this.zonesFrom[3] = [].slice.call(this.zonesFrom[3]).sort(sortByProp('zone_name'));

            return true
        },

        async testApi() {

            /*
            try {
                const responce = await axios.get(
                    'https://test.sakh-orch.ru',
                    {
                        params: {
                            testapi: '1'
                        }

                    },


                )
            } catch (e) {
                console.log(e)
            }

             */
            $.ajax
            ("https://test.sakh-orch.ru?testapi=1",
                {
                    type: "GET",
                    cache: false

                }).done(function (data) {
                pt.error = 'ok'

            }).fail(function (data) {

               // pt.error = data.responseText ?? 'yy'
            })
        },

    },

    Created() {


    },

    mounted(){
        this.testApi()

        this.setZones(1)
        this.getParams()
        this.getTipTops()
        return true
    },

    computed: {
        sortedList() {
            if(!this.packData.length)
                return []
            this.newTiptop()
            switch (this.sortParam) {
                case 'profit':
                    return this.filtredList.sort(sortByProfit);
                case 'profitor':
                    return this.filtredList.sort(sortByProfitOr);
                case 'salary':
                    return this.filtredList.sort(sortBySalary);
                case 'ZoneTo':
                    return this.filtredList.sort(sortByZoneTo);
                case 'ZoneFrom':
                    return this.filtredList.sort(sortByZoneFrom);
                default:
                    return this.filtredList;
            }
        },

        filtredList() {
            if(!this.packData.length)
                return []
            //let pData = this.packData
            let result = this.packData

            if(this.zFrom != 0){
                result = result.filter(pack => pack.Pack.zone_from === this.zFrom)
            }

            if(this.zTo != 0){
                result = result.filter(pack => pack.Pack.zone_to === this.zTo)
            }

            return result
        },


        errorMsg() {
            switch (this.error){
                case 'ok' : return 'ok'
                case 'side': return '??Восток, Север, Запад??';
                case 'notypes': return 'Не вижу тип пака';
                case 'no data': return 'Ничего не вижу';
                case 'user': return 'Ошибка авторизации';
                case 'dbError': return 'Ошибка записи в базу данных';
                default: return 'Ничего не понимаю';
            }

        },
        isred(){
            if(this.error !== 'ok'){
                return 'red';
            } else
                return 'gray';
        },
    }

}).mount('#rent')


