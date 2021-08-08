<?php
if (!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';
}
if (!$cfg->myip)
    die;

echo 'hghgdh';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Калькулятор себестоимости ресурсов Archeage."/>
    <meta name="keywords" content="Умный калькулятор, archeage, архейдж, крафт"/>
    <meta name=“robots” content=“index, nofollow”>
    <title>Цены пользователя</title>
    <link href="css/theme.css?ver=<?php echo md5_file('css/theme.css') ?>" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=0.7">
</head>

<body>
<div class="container pt-5" id="app" v-cloak>
    <div class="card">
        <h1>{{ title }}</h1>

        <div
                class="form-control"
                @keydown.down="key_activate($event)"
                @keydown.up="key_activate($event)"
                @keydown.enter="enter"

        >
            <h2>jhgfhj</h2>
            <input type="search" id="sIinput" @input="sQuery" ref="myInput" :value="inputValue" >
            <div v-if="sugCount">
                <div id="search_advice_wrapper" ref="slist">
                    <div
                            class="advice_variant"
                            :class="{'active': item.sel }"
                            v-for="(item,idx) in sResp"
                            :key="item.item_id"
                            :id="item.item_id"
                            @click="changeValue(item.item_name, idx)"
                    >
                        <img :src="getIcon(item.icon)"/> {{ item.item_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://unpkg.com/vue@next"></script>
<script src="js/app.js?<?php echo md5_file('js/prices.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>

