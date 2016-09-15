<html>
<head>
<script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
<script>
    var conn = new ab.Session('ws://elevator.com:8080',
        function() {
            conn.subscribe('doors', function(component, data) {
                // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                console.log('Doors component received a message "' + component + '" : ' + data.title);
            });
        },
        function() {
            console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
    );
</script>
</head>
<body>
here we are
</body>
</html>