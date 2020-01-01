<head>
<title>*</title>
</head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
<body>

<span id="count"></span>

<input id="x1" type="checkbox">
<input id="x2" type="checkbox">
<input id="x3" type="checkbox">

<a id="invert" href="#">invert</a>

<script type="text/javascript">

    var count = 0;

    $(function() {
        displayCount();
        $('input[type=checkbox]').click(function() {
            if (this.checked) {
                count++;
            } else {
                count--;
            }
            displayCount();
        });
        $('#invert').click(function(e) {
            e.preventDefault();
            $('input[type=checkbox]').click();
        });
    });

    function displayCount() {
        $('#count').text(count);
    }

</script>

</body>
</html>