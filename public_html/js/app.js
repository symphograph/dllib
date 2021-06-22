VueGlobalProd.createApp({
    data: () => ({
        counter: 0,
        title: 'Список заметок',
        plsHld: 'Напишите что-нибудь',
        inputValue: 'пр',
        notes: ['Заметка 1', 'Заметка 2'],
        btnName: 'Удалить',
        person: {
            firstName: 'Имя',
            lastName: 'Фамилия',
            age: 27
        },
        responceData: 'gfdsfg333',
        errored: false,
        myItems: {},
        formData: {
            fName: '',
            aje: ''
        },
        fData: {},
        sValue : '',
        sResp: {},
        sugCount: 0,
        inputInitValue: '',
        sugSelected: 0

    }),

    methods: {
        getItems: function () {

            this.fData = new FormData(document.getElementById('myForm'))

            axios({
                method: 'post',
                url: 'hendlers/vuetest.php',
                data: this.fData
            })
                .then((response) => {
                    this.myItems = response.data;
                    console.log(this.myItems);
                })
                .catch(function (error) {
                    console.log(error);
                });

        },

        sQuery() {
            let myQuery =  this.$refs.myInput.value;
            if(myQuery.length > 2){
                this.inputInitValue = myQuery
                this.inputValue = myQuery
                this.sendQuery(myQuery)
            }

        },

        sendQuery(myQuery){
            axios({
                method: 'post',
                url: 'hendlers/searchjson.php',
                data: {
                    query: myQuery
                }
            })
                .then((response) => {
                    if (Array.isArray(response.data)){
                        this.sResp = response.data;
                        this.sugCount = this.sResp.length
                        this.SelCleaner()
                    }else
                        this.hideWrapp()

                    //console.log(this.sResp)
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

        changeValue(text,idx){
            this.inputValue = text
            this.SelCleaner()
            this.sResp[idx].sel = true
            this.hideWrapp()
        },

        addNewNote() {
            if (this.inputValue.length >= 3)
                this.notes.push(this.inputValue)

        },

        removeItem(idx) {
            //console.log(this.myItems)
            this.sResp.splice(idx, 1)

        },
        getIcon(icon) {
            return 'img/icons/50/' + icon + '.png'
        },

        key_activate(event){
            this.SelCleaner()

            if(event.key === 'ArrowDown' && this.sugSelected < this.sugCount){
                this.sugSelected++;
            }else if(event.key === 'ArrowUp' && this.sugSelected > 0){
                this.sugSelected--;
            }

            this.sEnter()

        },

        enter(){
            this.sEnter()
            this.hideWrapp()
            document.getElementById("sIinput").focus()
        },

        sEnter(){

            if(this.sugSelected > 0){
                this.sResp[this.sugSelected-1].sel = true
                this.inputValue = this.sResp[this.sugSelected-1].item_name

            } else {
                this.inputValue = this.inputInitValue
            }
        },

        SelCleaner(){
            for (let i in this.sResp){
                this.sResp[i].sel = false;
            }
        },

        hideWrapp() {
            this.sResp = {}
            this.sugCount = 0
        },

},



    mounted(){
        document.addEventListener('click', this.hideWrapp.bind(this), true)
    },

    beforeUnmount(){
        document.removeEventListener('click',this.hideWrapp)
    }


}).mount('#app')
