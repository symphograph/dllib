const app = Vue.createApp( {
    data() {
        return {
            per: 130,
            siol: 0,

            packData: []
        }
    },

    methods: {
        test(){
            console.log($('#packsettings').serialize())
            let form = $('#packsettings')
            $.ajax
            ({

                url: "hendlers/packs_list.php", // путь к ajax файлу
                type: "POST",      // тип запроса

                data: form.serialize(),

                dataType: "json",
                cache: false,
                // Данные пришли
                success: function(data )
                {
                    //$jdun.removeClass("loading"); $jdun.addClass("jdun");

                    //$("#input_data").html(data );
                    pt.packData = data;
                    console.log(pt.packData)



                    //packList(data)
                   // TipTop();
                }
            });
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
        }
    },
    computed: {


    }

})

const pt = app.mount('#rent')