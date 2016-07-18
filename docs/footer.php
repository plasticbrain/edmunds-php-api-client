    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <script src="assets/scripts/prism.js"></script>
    <script src="assets/scripts/toc.min.js"></script>

    <script>
        (function(){    
            Prism.plugins.NormalizeWhitespace.setDefaults({
                'remove-trailing': true,
                'remove-indent': true,
                'left-trim': true,
                'right-trim': true,
                'remove-initial-line-feed': true,
                'spaces-to-tabs': 4
                /*
                'break-lines': 80,
                'indent': 2,
                'tabs-to-spaces': 4,
                */
            });




        });

        $(function() {

            $('#toc').toc({
                'selectors': 'h3,h4', //elements to use as headings
                'container': '#content', //element to find all selectors in
                'smoothScrolling': true, //enable or disable smooth scrolling on click
                'prefix': 'toc', //prefix for anchor tags and class names
                'onHighlight': function(el) {}, //called when a new section is highlighted 
                'highlightOnScroll': true, //add class to heading that is currently in focus
                'highlightOffset': 100 //offset to trigger the next headline
            });

            $(document).on('click', '.btn-do-example', function() {
                var apiKey = $('#api_key').val();

                var resDiv = $('#example-response');
                var resDivCode = resDiv.find('#api-response');

                el = $(this);
                section = el.attr('data-api-section');
                resource = el.attr('data-api-resource');
                method = el.attr('data-api-method');

                $.ajax({
                    url: 'inc/ajax.php',
                    type: 'POST',
                    data: {section: section, resource: resource,  method: method, apiKey: apiKey},
                })
                .always(function(res) {
                    if (!res || !res.success || res.success == false || !res.data) {
                        
                        console.log(res ? res : 'No data received');

                        if (res.message) {
                            resDivCode.text(res.message);
                        } else {
                            resDivCode.text('Error: No data received');
                        }
                    } else {
                        resDivCode.text(res.data);
                    }
                });
                

                return false;
            });
        });

    </script>
</body>
</html>