$('#fol_form')
    .on('change', 'input[type="checkbox"]',
        function () {
            chFolow(this.value, this.checked)
        });

$('#all_info').on('click', '#sendnick', function () {
    var nick = $('#public_nick').val();
    var valid = NickValid(nick);
    if (valid)
        SetNick(nick);
});

$('#all_info').on('input', '#public_nick', function () {
    var nick = $('#public_nick').val();
    NickValid(nick);
});