

const app = Vue.createApp({

    data() {
        return {
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
            isbuy: 1
        }
    },

    components: {
        'price-form2':
            window.httpVueLoader('js/items/components/price-form2.vue')
    },

    watch: {

        isbuy: {
            handler(val){
                console.log(this.isbuy)
            }
        },

        itemId: {

            handler(val){
                this.getItem()
            },
            deep: true
        },



        search: {
            handler(val){
                this.getSearch()
                this.items = {}
                //this.itemId = 0
            },
            deep: true
        },

        sGrId:{
            handler(val){
                this.getCategs()
                this.items = {}
                this.itemId = 0
                this.searched = {}
            },
            deep: true

        },

        curCat:{
            handler(val){
                this.itemId = 0
                this.getItemList()
            },
            deep: true
        },
    },

    methods: {

        pchId(val){
            this.itemId = val
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
            ("hendlers/items/set_prices.php",
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

        lostPrice(idx,price){
            price = '' + price

            this.curItem.lost[idx].auc_price = price
            price = price.replace(/[^\d]/g,'')
            if(price == 0){
                price = 0
            }

            let len = price.length;
            if(len>2){
                price = price.substring(0, len-2) + " " + price.substring(len-2);
                len = price.length;
            }
            if(len>5){
                price = price.substring(0, len-5) + " " + price.substring(len-5);
            }


            this.curItem.lost[idx].auc_price = price

            return price
        },

        setPrice(itemId,Price){
            Price = '' + Price;
            Price = Price.replace(/[^\d]/g,'')

            $.ajax
            ("hendlers/items/set_price.php",
                {
                    type: "POST",
                    data: {
                        itemId: itemId,
                        Price: Price

                    },
                    dataType: "json",
                    cache: false,
                    headers: {}
                }).done(function (data) {
                pt.error = 'ok'
                pt.getPrice()

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
                console.log(pt.error)
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
                pt.pricez = pt.curItem.priceData.price
                pt.error = 'ok'

            }).fail(function (data) {
                pt.curItem = {}
                pt.error = data.responseText ?? 'yy'
                console.log(pt.error)
            })


        },

        getCrafts(){
            if(!this.itemId){
                return
            }
            this.curItem.crafts = {}

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
                pt.curItem.crafts = data.item.crafts
                pt.error = 'ok'

            }).fail(function (data) {
                pt.curItem.crafts = {}
                pt.error = data.responseText ?? 'yy'
                console.log(pt.error)
            })
        },

        navgatItem(event){

            switch (event.keyCode) {
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
                console.log(pt.error)
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
                console.log(error);
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
                console.log(response.data)
            })
            .catch(function(error) {
                console.log(error);
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
                //console.log(pt.catList)
            })
            .catch(function(error) {
                console.log(error);
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
            this.getPrice()
        },

        delPrice(itemId){
            $.ajax
            ("hendlers/items/del_price.php",
                {
                    type: "POST",
                    data: {
                        itemId: itemId
                    },
                    dataType: "json",
                    cache: false,
                    headers: {

                    }
                }).done(function (data) {
                pt.error = 'ok'

            }).fail(function (data) {
                pt.error = data.responseText ?? 'yy'
                console.log(pt.error)
            })
        },

        getPrice(){
            $.ajax
            ("hendlers/items/get_price.php",
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
                console.log(pt.error)
            })
        },

        valutImager(value,vid = 500){
            value = '' + value;
            value = value.replace(/[^\d]/g,'')
            if(vid !== 500){
                return value + '<img src="../img/icons/50/'+vid+'.png" '+'style="width: 0.9em; height: 0.9em"  alt="v"/>';
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


    },

    mounted() {
        this.getSubGroups()
        var itid = document.getElementById("current").value;
        if(itid){
            this.itemId = itid
        }
        return true
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

        pricez: {

            get() {
                return this.price;
            },

            set(val) {
                let str = '' + val;
                this.price = str
                str = str.replace(/[^\d]/g,'')
                if(str == 0){
                    str = 0
                }

                let len = str.length;
                if(len>2){
                    str = str.substring(0, len-2) + " " + str.substring(len-2);
                    len = str.length;
                }
                if(len>5){
                    str = str.substring(0, len-5) + " " + str.substring(len-5);
                }


                this.price = str
            }

        }




    },

})
app.component('craft-info',{
    props: ['arr-props','uamount','icon','grade'],
    data () {
        return {
            itemId: 0,
            pref: {
                0: 'Предпочитать этот',
                1: '',
                2: 'Сбросить выбор',
                3: 'Сбросить выбор'
            }
        }
    },
    methods: {
        valutImager(value,vid = 500){
            value = '' + value;
            value = value.replace(/[^\d]/g,'')
            if(vid !== 500){
                return value + '<img src="../img/icons/50/'+vid+'.png" '+'style="width: 0.9em; height: 0.9em"  alt="v"/>';
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

        chId(id){
            this.$emit('chid',id)
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
    template: `
<div class="craftCard">
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
                <div><div class="esyprice" v-html="valutImager(arrProps.profitor,500)"></div></div>
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
                    :data-tooltip="pref[arrProps.prefType]"
                     :style="{ backgroundImage: 'url(img/icons/50/'+icon+'.png)'}">
                <div class="grade"
                     data-tooltip=""
                     :style="{ backgroundImage: 'url(img/grade/icon_grade'+grade+'.png)'}">
                    <div class="matneed">{{ arrProps.result_amount  * uamount }}</div>
                </div>
            </div>
            <template v-for="mat in arrProps.mats">
            <label class="cubik">
                <div class="itim" id="itim_8318" :style="{ backgroundImage: 'url(img/icons/50/'+mat.icon+'.png)'}">
                    <div class="grade" :data-tooltip="mat.tooltip" 
                        :style="{ backgroundImage: 'url(img/grade/icon_grade'+mat.need_grade+'.png)'}">
                        <div class="matneed">{{ mat.mater_need  * uamount }}</div>            
                    </div>
                </div>
                <input class="hide" type="radio" @change="chId(mat.item_id)" name="item" :value="mat.item_id">
                </label>
            </template>
        </div>
    </div>
</div>
</div>
`
})
app.component('price-input', {
    props: ['price'],
    data() {
        return {
            /*price: 123*/
        }
    },
    emits: ['update:pricez'],
    computed: {
        pricez: {

            get() {
                console.log(this.price)
                return this.price;

            },

            set(val) {
                let str = '' + val;
                str = str.replaceAll(' ','')
                console.log(str)
                let len = str.length;
                if(len>2){
                    str = str.substring(0, len-2) + " " + str.substring(len-2);
                    len = str.length;
                }
                if(len>5){
                    str = str.substring(0, len-5) + " " + str.substring(len-5);
                }

                this.$emit('pricez', str);
                this.price = str
            }

        },
    },
    template: `<input :value="pricez" @input="$emit('update:pricez', $event.target.value)">`
})


const pt = app.mount('#rent')

