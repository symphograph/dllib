function toNums(val) {
    val += ''
    val = +val.replace(/[^\d]/g, '')
    return val
}

function priceStringer(str) {
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
}

function valutImager(value, vid = 500) {
    let minus = ''
    if (value < 0) {
        minus = '-'
    }
    value = toNums(value)
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
}

function goToItem(item) {

    item = toNums(item)
    if (item) {
        localStorage.setItem('item', item);
        document.location.href = "catalog.php";
    }
}