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
            packForm: {
                per: 130,
                pack_age: 0,
                side: null,
                siol: 0,
                type: [],
            },
            error: 'ok',
            color: 'gray'

        }
    },
    watch: {
        packForm:{
            handler(val){
                this.getPacks()
            },
            deep: true

            }

    },

    methods: {
        getPacks(){
            //console.log($('#packsettings').serialize())
            let form = $('#packsettings')
            let fdata = form.serialize()

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
                pt.packData = data

                //console.log(pt.packData)
                //console.log(pt.sortParam)
                //console.log(pt.sortParam)
                TipTop();

            }).fail(function(data) {
                pt.jdunOff()
                //console.log(data.responseText)
                pt.packData = []
                pt.error = data.responseText ?? 'yy'
                $('#tiptop').html(pt.errorMsg)
                //$('#tiptop').html('Похоже, что-то не получается... :(')
            })
            //pt.sortedList()
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
        }

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
                default: return 'Ничего не понимаю';
            }

        },
        isred(){
            if(this.error !== 'ok'){
                return 'red';
            } else
                return 'gray';
        }

    }

}).mount('#rent')


