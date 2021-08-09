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
                side: null,
                siol: 0,
                type: [],
            },
            uPrices: {},
            error: 'ok',
            color: 'gray',
            min: 0

        }
    },
    watch: {
        packForm:{
            handler(val){
                this.getPacks()
            },
            deep: true

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
            pt.lost = []
            pt.uPrices = []
            localStorage.setItem ("packTypes", JSON.stringify(pt.packForm.type));
            if(!this.packForm.side){
                this.error = 'side'
                $('#tiptop').html(this.errorMsg)
                console.log(this.errorMsg)
                return false
            }

            this.jdunOn()
            $.ajax
            ({
                url: "hendlers/packs_list.php",
                type: "POST",
                data: this.packForm,
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

                TipTop();

            }).fail(function(data) {
                pt.jdunOff()

                pt.packData = []
                pt.lost = []
                pt.error = data.responseText ?? 'yy'
                $('#tiptop').html(pt.errorMsg)
            })

        },

        valutImager(value,vid = 500){
            if(vid !== 500){
                return value + '<img src="../img/'+vid+'.png" '+'style="width: 0.9em; height: 0.9em"  alt="coal"/>';
            }
            var str = '' + value;

            let row = '';
            let len = str.length;
            for (var i = 0; i < len; ++i) {

                if(len - i === 2 && len>2){
                    row+='<img src="img/silver.png" style="width: 0.9em; height: 0.9em" alt="s"/>';
                }
                if(len - i === 4 && len>4){
                    row+='<img src="img/gold.png" style="width: 0.9em; height: 0.9em" alt="g"/>';
                }
                row += str.charAt(i);

                //console.log(row);
            }
            row+='<img src="img/bronze.png" style="width: 0.9em; height: 0.9em" alt="b"/>';
            return row;
        },

        jdunOn() {
            let jdun = $("#jdun");
            jdun.removeClass("jdun"); jdun.addClass("loading");
        },

        jdunOff() {
            let jdun = $("#jdun");
            jdun.removeClass("loading"); jdun.addClass("jdun");
        },

        getParams() {
            var ls = localStorage.getItem('packTypes');
            if (ls === null) {
                return false;
            }
            if (JSON.parse(ls).length) {
                this.packForm.type = JSON.parse(ls)
                return true
            } else {
                localStorage.setItem("packTypes", JSON.stringify([]));
            }
        },

        setParams(){

        },

        goToItem(item){

            item = this.toNums(item)
            if(item){
                localStorage.setItem ('item',item);
                document.location.href = "catalog.php";
            }

        },

        toNums(val){
            val += ''
            val = +val.replace(/[^\d]/g,'')
            return val
        }

    },

    mounted(){

        this.getParams()
       // console.log(this.$route.query)
        return true
    },

    computed: {
        sortedList() {
            switch (this.sortParam) {
                case 'profit':
                    return this.packData.sort(sortByProfit);
                case 'profitor':
                    return this.packData.sort(sortByProfitOr);
                case 'salary':
                    return this.packData.sort(sortBySalary);
                case 'ZoneTo':
                    return this.packData.sort(sortByZoneTo);
                case 'ZoneFrom':
                    return this.packData.sort(sortByZoneFrom);
                default:
                    return this.packData;
            }
        },
        errorMsg() {
            switch (this.error){
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


