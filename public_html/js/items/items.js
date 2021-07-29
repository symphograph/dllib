
const pt = Vue.createApp({

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
            error: 'ok'
        }
    },
    watch: {

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
                //this.itemId = 0
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

        getItem(){

            if(!this.itemId){
                return
            }

            this.searched = {}
            this.search = ''
            this.items = {}
            this.catList = {}
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
                    headers: {

                    }
                }).done(function (data) {
                pt.curItem = data;
                pt.error = 'ok'

            }).fail(function (data) {
                pt.curItem = {}
                pt.error = data.responseText ?? 'yy'
                console.log(pt.error)
            })


        },

        navgatItem(event){
            console.log(event.keyCode)
            switch (event.keyCode) {
                case 13:
                    if(this.focusRow){
                        this.itemId = this.searched[this.focusRow].item_id
                    }
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

            console.log(cHeihgt)

            document.getElementById('items').scrollTo({top: offset , behavior: 'auto'});
        },

        getSearch(){
            if(this.search.length < 3){
                return
            }


            this.items = {}
            this.catList = {}

            /*
            axios({
                method: 'post',
                url: 'hendlers/items/search.php',
                responseType: 'json',
                data: {
                    search: this.search,
                },
                headers: {
                    "Content-type": "application/json; charset=UTF-8"
                }
            })
            .then(function(response) {
                console.log(response)
                pt.searched = response.data;
            })
            .catch(function(error) {
                console.log('errrrr');
            });*/


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
        }
    },
    mounted() {
        this.getSubGroups()
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
    }

}).mount('#rent')

//pt.use(VueAxios, axios)

