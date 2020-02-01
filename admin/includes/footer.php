

<div class='fixed-bottom text-center p-3'>
&copy; Copyright 2019 Simplicity
</div>


<!-- SCRIPTS -->
<script src="/ssp/js/jquery.min.js"></script>
<script src="/ssp/js/bootstrap.min.js"></script>
<!-- <script src="https://kit.fontawesome.com/10c3c5053e.js"></script> -->
<script>
    function updateSizes(){
        let sizeString = "";
        for(let i = 1; i<=5; i++){
            if(jQuery('#size'+i).val() != ""){
                sizeString += jQuery('#size'+i).val() + ' : ' + jQuery('#qty'+i).val() + ", ";
            }
        }
        sizeString = sizeString.replace(/,\s*$/, "");
        jQuery('#qty_size').val(sizeString);
    }

    function get_child_options(){
        var parentID = jQuery('#parent_for').val();
        jQuery.ajax({
            url: '/ssp/admin/parser/child_categories.php',
            type: 'POST',
            data: {parentID : parentID},
            success: function(data){
                 jQuery('#child_for').html(data);
            },
            error: function(){
                alert("something went wrong with the child options");
            }
        })
    }
    jQuery('select[name="parent_isset"]').change(function(){
        get_child_options();
    });
</script>

<!-- END SCRIPTS -->
</body>
</html>