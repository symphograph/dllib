const sortCraftsBySPM = (d1, d2) =>(d1.spmu > d2.spmu) ? 1 : -1;

const app = Vue.createApp({

    data() {
        return {
            startServer: true,
            startMode: true,
            subCategs: {},
            sGrId: 0,
            view: 0,
            catList: {},
            items: {},
            curCat: 0,
            itemId: 0,
            curItem: {},
            focusRow: null,
            search: '',
            searched: {},
            error: 'ok',
            price: 1,
            Uamount: 1,
            isbuy: 1,
            prefType: 0,
            prefText: {
                1: 'Выбран автоматически',
                2: 'Выбран вами',
                3: 'Покупается'
            },
            unset: 'Сбросить',
            open: '',
            tiptops: [{tip_text:''}],
            tiptop: '',
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
            modes: {
                1: 'С миру по нитке',
                2: 'Доверие',
                3: 'Хардкор'
            },
            mode: 0
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

        mode: {
            handler(val){
                if(!this.startMode){
                    this.setMode()
                }else{
                    this.startMode = false
                }
            }
        },



        isbuy: {
            handler(val){
                this.setIsBuy()
            }
        },

        itemId: {

            handler(val){
                if (this.itemId){
                    localStorage.setItem ('item',this.itemId);
                }

                this.getItem()
                this.tiptopp()
            },

        },

        search: {
            handler(val){
                if (!val || val.length < 3){
                    this.searched = {}
                }
                this.getSearch()
                this.items = {}
                //this.itemId = 0
            },
            deep: true
        },

        sGrId:{
            handler(val){
                if(!this.sGrId){
                    return
                }
                this.getCategs()
                this.items = {}
                this.itemId = 0
                this.searched = {}
            },
            deep: true

        },

        curCat:{
            handler(val){
                if(!this.curCat){
                    return
                }
                this.itemId = 0
                this.getItemList()
            },
            deep: true
        },
    },

    methods: {

        copy(val){
            try{
                navigator.clipboard.writeText(val)
            } catch (e) {
                throw e
            }
        },

        tiptopp() {
            this.tiptop = this.tiptops[Math.floor(Math.random()*this.tiptops.length)].tip_text;
        },

        getTipTops(){
            $.ajax
            ("hendlers/tiptops.php",
                {
                    type: "POST",
                    data: {
                        tiptop: 1
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.tiptops = data
                pt.tiptopp()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })

        },

        pchId(val){
            this.itemId = val
        },

        setBest(newBest){

            $.ajax
            ("hendlers/items/set_best.php",
                {
                    type: "POST",
                    data: {
                        craft_id: newBest
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.getCrafts()
                let target = document.getElementById('isbuy');
                let contaner = document.getElementById('items')
                contaner.scrollTo({top: target.offsetTop - 50, behavior: 'smooth'});

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })

        },

        setLostPrices(){
            let arr = this.curItem.lost

            let prices = []
            let i = 0
            arr.forEach(function(item) {
                if(item.auc_price){
                    item.auc_price = item.auc_price.replace(/[^\d]/g,'')
                    prices[i] = {item_id: item.item_id,price: item.auc_price}
                    i++
                }
            });
            if(!prices.length){
                return false
            }

            $.ajax
            ("hendlers/price/set_prices.php",
                {
                    type: "POST",
                    data: {
                        prices: prices
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.getItem()

            }).fail(function (data) {
                    pt.error = data.responseText ?? 'yy'
            })

        },

        resetBest() {
            $.ajax
            ("hendlers/items/reset_best.php",
                {
                    type: "POST",
                    data: {
                        item_id: this.itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.getCrafts()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        setIsBuy(){
            $.ajax
            ("hendlers/isbuyset.php",
                {
                    type: "POST",
                    data: {
                        isbuy: this.isbuy,
                        item_id: this.itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.getCrafts()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        lostPrice(idx,price){

            price = '' + price
            price = price.replace(/^0+/, '');

            this.curItem.lost[idx].auc_price = price
            price = this.priceStringer(price)

            this.curItem.lost[idx].auc_price = price

            return price
        },

        setPrice(itemId,Price){
            Price = toNums(Price)

            $.ajax
            ("hendlers/price/set_price.php",
                {
                    type: "POST",
                    data: {
                        item_id: itemId,
                        price: Price

                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                //pt.getPrice()
                pt.getItem()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
               // console.log(pt.error)
            })
        },

        getItem(){

            if(!this.itemId){
                return
            }

            this.searched = {}
            this.search = ''
            this.items = {}
            this.catList = {}
            this.focusRow = 0
            this.tiptop
            this.curCat = 0
            this.sGrId = 0

            $.ajax
            ("hendlers/items/item.php",
                {
                    type: "POST",
                    data: {
                        itemId: this.itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
            }).done(function (data) {
                pt.curItem = data.item;
                pt.curItem.isBuyCraft ? pt.isbuy = 3 : pt.isbuy = 1

                pt.pricez = pt.curItem.priceData.price
                pt.error = 'ok'

            }).fail(function (data) {
                pt.curItem = {}
                pt.error = data.responseText ?? 'yy'

            })


        },

        reloadItem(){
            //let tmp = this.itemId
            //this.curItem = {}
            this.Uamount = 1
            this.getItem()
        },

        getCrafts(){
            if(!this.itemId){
                return
            }
            //this.curItem.crafts = {}

            $.ajax
            ("hendlers/items/item.php",
                {
                    type: "POST",
                    data: {
                        itemId: this.itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.curItem = data.item
                //pt.curItem.crafts = data.item.crafts
                //pt.curItem.bestCraft = data.item.bestCraft
                //pt.curItem.bestCraftId = data.item.bestCraftId

                $('.tooltip').remove()
                pt.error = 'ok'

            }).fail(function (data) {
                pt.curItem.crafts = {}
                pt.error = data.responseText ?? 'yy'
               // console.log(pt.error)
            })
        },

        navgatItem(event){

            switch (event.keyCode) {
                case 27:
                    this.searched = {}
                    break
                case 13:
                    this.itemId = this.searched[this.focusRow].item_id
                    this.searched = {}
                    break
                case 38:
                    if (this.focusRow === null) {
                        this.focusRow  = 0;
                    } else if (this.focusRow  > 0) {
                        this.focusRow --;
                    }
                    break;
                case 40:
                    if (this.focusRow  === null) {
                        this.focusRow  = 0;
                    } else if (this.focusRow  < this.searched.length - 1) {
                        this.focusRow ++;
                    }
                    break;

            }

            var cHeihgt = document.getElementById('items').clientHeight
            var offset = 47 * (this.focusRow + 2) - cHeihgt

            document.getElementById('items').scrollTo({top: offset , behavior: 'auto'});
        },

        getSearch(){
            if(this.search.length < 3){
                return
            }


            this.items = {}
            this.catList = {}

            $.ajax
            ("hendlers/items/search.php",
                {
                    type: "POST",
                    data: {
                        search: this.search
                    },
                    dataType: "json",
                    cache: false,
                    headers: {

                    }
                }).done(function (data) {
                pt.searched = data;
                pt.error = 'ok'

            }).fail(function (data) {
                pt.searched = {}
                pt.error = data.responseText ?? 'yy'
            })
        },

        getSubGroups(){

            axios({
                method: 'post',
                url: 'hendlers/items/subgroups.php',
                data: {
                    title: 'new_title',
                    body: 'new_body',
                    userId: 'userid'
                },
                headers: {
                    "Content-type": "application/json; charset=UTF-8",
                    "HTTP_X_CSRF_TOKEN": "12345"
                }
            })
            .then(function(response) {
                pt.subCategs = response.data;
            })
            .catch(function(error) {
                //console.log(error);
            });

        },

        getCategs(){
            if(!this.sGrId){
                return
            }

            axios({
                method: 'post',
                url: 'hendlers/items/categs.php',
                data: {
                    sGrId: this.sGrId,
                },
                headers: {
                    "Content-type": "application/json; charset=UTF-8"
                }
            })
            .then(function(response) {

                pt.catList = response.data;
            })
            .catch(function(error) {
                //console.log(error);
            });

        },

        getItemList(){
            axios({
                method: 'post',
                url: 'hendlers/items/itemlist.php',
                data: {
                    curCat: this.curCat,
                },
                headers: {
                    "Content-type": "application/json; charset=UTF-8"
                }
            })
            .then(function(response) {

                pt.items = response.data;

            })
            .catch(function(error) {
               // console.log(error);
            });

        },

        setSGroupId(val){
            pt.sGrId = val
            return true;
        },

        itemTooltip(name,craftable, personal){
            crft = craftable ? '<br>[Крафтабельный]' : ''
            pers = personal ? '<br>[Персональный]' : ''
            return name + crft + pers
        },

        delMainPrice(){
            this.delPrice(this.curItem.item_id)
            this.reloadItem()
        },

        setServer(){
            $.ajax
            ("hendlers/set_server.php",
                {
                    type: "POST",
                    data: {
                        server: this.server
                    },
                    dataType: "json",
                    cache: false,
                    headers: {

                    }
                }).done(function (data) {
                pt.error = 'ok'
                pt.reloadItem()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        setMode(){
            $.ajax
            ("hendlers/items/set_mode.php",
                {
                    type: "POST",
                    data: {
                        mode: this.mode
                    },
                    dataType: "json",
                    cache: false,
                    headers: {

                    }
                }).done(function (data) {
                pt.error = 'ok'
                pt.reloadItem()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
            })
        },

        delPrice(itemId){
            $.ajax
            ("hendlers/price/del_price.php",
                {
                    type: "POST",
                    data: {
                        item_id: itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {

                    }
                }).done(function (data) {
                pt.error = 'ok'

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
              //  console.log(pt.error)
            })
        },

        round(val)
        {
            return +val.toFixed(2)
        },

        getPrice(){
            $.ajax
            ("hendlers/price/get_price.php",
                {
                    type: "POST",
                    data: {
                        itemId: this.curItem.item_id
                    },
                    dataType: "json",
                    cache: false,
                    headers: {

                    }
                }).done(function (data) {
                pt.error = 'ok'
                pt.curItem.priceData = data

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
                //console.log(pt.error)
            })
        },

        valutImager(value,vid = 500){
            let minus = ''
            if (value < 0) {
                minus = '-'
            }
            value = '' + value;
            value = value.replace(/[^\d]/g, '')
            if (vid !== 500) {
                return value + '<img src="../img/icons/50/' + vid + '.png" ' + 'style="width: 0.9em; height: 0.9em"  alt="v"/>';
            }
            var str = '' + value;

            let row = '';
            let len = str.length;
            for (var i = 0; i < len; ++i) {

                if (len - i === 2 && len > 2) {
                    row += '<img src="img/silver.png" style="width: 0.9em; height: 0.9em" alt="s"/>';
                }
                if (len - i === 4 && len > 4) {
                    row += '<img src="img/gold.png" style="width: 0.9em; height: 0.9em" alt="g"/>';
                }
                row += str.charAt(i);


            }
            row = minus + row
            row += '<img src="img/bronze.png" style="width: 0.9em; height: 0.9em" alt="b"/>';
            return row;
        },

        cleanSearch(){

            //this.searched = {}
            //this.search = ''
        },

        priceStringer(str){
            return priceStringer(str)
        },

        toNums(val){
            val += ''
            val = +val.replace(/[^\d]/g,'')
            return val
        }
    },

    mounted() {



        let item = localStorage.getItem('item') ?? 0;
        item = this.toNums(item)
        if(item){
            this.itemId = item
        }

        this.getSubGroups()
        this.getTipTops()

        let userver = document.getElementById("server").value;
        if(userver){
            this.server = userver
        }

        let umode = document.getElementById("mode").value;
        if(umode){
            this.mode = umode
        }

        return true
    },

    Created() {

    },

    computed: {
        errorMsg() {
            switch (this.error){
                case 'ok': return  'ok';
                case 'side': return '??Восток, Север, Запад??';
                case 'notypes': return 'Не вижу тип пака';
                case 'no data': return 'Ничего не вижу';
                case 'user': return 'Ошибка авторизации';
                case 'dbError': return 'Ошибка записи в базу данных';
                default: return 'Ничего не понимаю';
            }
        },



        sortedCrafts() {
            if(!this.curItem.craftable){
                return
            }
            let crArr = this.curItem.crafts.other
            return crArr.sort(sortCraftsBySPM);
            //return  Object.fromEntries(crArr)
        },

        pricez: {

            get() {
                return this.price;
            },

            set(val) {

                let str = '' + val;
                str = str.replace(/^0+/, '');


                this.price = str
                str = this.priceStringer(str)

                this.price = str
            }

        }

    },

})
app.component('craft-info',{
    props: ['arr-props','uamount','icon','grade','pref-text'],
    data () {
        return {
            itemId: 0,
            pref: {
                0: 'Предпочитать этот',
                1: '',
                2: 'Сбросить выбор',
                3: 'Сбросить выбор'
            },

            show: true,

        }
    },
    methods: {
        valutImager(value,vid = 500){
            return valutImager(value,vid)
        },

        chId(id){
            this.$emit('chid',id)
        },

        setBest(id){
            if(!this.arrProps.prefType){
                this.show = !this.show
                this.$emit('setbest',id)
            }else {
                this.$emit('reset',id)
            }
        },

        spaceReplace(str){
            return  str.replaceAll(' ','_')
        },

        inArray(needle, haystack) {
            var length = haystack.length;
            for(var i = 0; i < length; i++) {
                if(haystack[i] == needle) return true;
            }
            return false;
        }
    },
    computed:{
        profNeed() {
            if(this.arrProps.prof_need >= 1000){
                return this.arrProps.prof_need / 1000 + 'k'
            }else{
                return this.arrProps.prof_need
            }
        },

        profit() {
            return this.arrProps.craft_price
        }
    },
    template: `<transition name="bounce">
<div class="craftCard" v-if="show" :id="'craft_' + arrProps.craft_id">
<div class="craftinfo">
    <div>
        <div class="crresults"><div></div></div>
    </div>
    <div>
        <div class="crresults"><div><b>{{arrProps.craft_name}}</b></div></div>
    </div>
</div>
<div class="craftinfo">
    <div>
        <div class="crresults">
            <div>
                <img :src="'img/profs/' + spaceReplace(arrProps.prof_name) + '.png'" style="width: 20px; height: 20px">
                {{arrProps.prof_name}}                    
            </div>
            <div>{{profNeed}}</div>
        </div><hr>
    </div>
    <div>
        <div class="crresults"><div>{{arrProps.dood_name}}</div></div>
    </div>
</div>
<div class="craftinfo">
    <div>
        <div class="crresults" data-tooltip="Коэфициент приводящий друг к другу такие величины, как занимаемая площадь, интервал между сбором, себестоимость, получаемое количество, требуемое количество.">
            <div>Коэф SPM:</div>
            <div>{{arrProps.spmu}}</div>
        </div>
    </div>
</div>
<div class="craftinfo">
    <div>
        <div class="crresults">
            <div>Себестоимость 1 шт:</div>
            <div v-html="valutImager(arrProps.craft_price,500)"></div>
        </div>
        <div v-if="arrProps.isGoldable">
            <div class="crresults">
                <div>Прибыль:</div>
                <div><div class="esyprice" v-html="valutImager(arrProps.profit,500)"></div></div>
            </div>
            
            <div class="crresults">
                <div>Прибыль с 1 ОР:</div>
                <div><div class="esyprice" v-html="valutImager(arrProps.profitor,500)"></div></div>
            </div>
        </div>
    </div>
    <div>
    <div class="crresults">
        <div>Интервал:</div>
        <div>{{arrProps.sptime}}</div>
    </div>
    <div class="crresults">
        <div>На рецепт:</div>
        <div>{{arrProps.labor_need2}}<img src="../img/icons/50/2.png" class="smallIcon"></div>
    </div>
        <div class="crresults">
        <div>На 1 шт:</div>
        <div>{{arrProps.labor_single}}<img src="../img/icons/50/2.png" class="smallIcon"></div>
    </div>
        <div class="crresults">
        <div>На цепочку</div>
        <div>{{arrProps.labor_total}}<img src="../img/icons/50/2.png" class="smallIcon"></div>
    </div>
    			</div>
</div>

<div class="crftarea">
    <div class="matarea">
        <div class="matrow">
            <div class="main_itim"
                    :data-tooltip="prefText"
                     :style="{ backgroundImage: 'url(img/icons/50/'+icon+'.png)'}"
                     @click="setBest(arrProps.craft_id)"
                     >
                <div class="grade"
                     :style="{ backgroundImage: 'url(img/grade/icon_grade'+grade+'.png)'}">
                    <div class="matneed">{{ arrProps.result_amount  * uamount }}</div>
                </div>
            </div>
            <template v-for="mat in arrProps.mats">
                <label class="cubik" @click="chId(mat.item_id)">
                    <div class="itim" id="itim_8318" :style="{ backgroundImage: 'url(img/icons/50/'+mat.icon+'.png)'}">
                        <div class="grade" 
                            :data-tooltip="mat.tooltip" 
                            :style="{ backgroundImage: 'url(img/grade/icon_grade'+mat.need_grade+'.png)'}">
                            <div class="matneed">{{ mat.mater_need  * uamount }}</div>            
                        </div>
                    </div>       
                </label>
            </template>
        </div>
    </div>
</div>
</div></transition>
`
})

const pt = app.mount('#rent')

