<?php
require_once(dirname(__FILE__)."/includes/functions.php");
page_head("Letná liga FLL");
page_nav();
get_topright_form();
?>

        <div id="content">

            <script>
                $(document).ready(function(){
                    $.ajax({
                        async: true,
                        type: "GET",
                        data: {<?php if (isset($_GET['year'])) echo 'year:'.$_GET['year'];?>},
                        url: "includes/show_result_tables.php"
                    }).done(function (data) {
                        $("#results").html(data);
                    })
                });
            </script>

            <h2 class="center"><span data-trans-key="results-of-league"></span> <?php echo isset($_GET['year']) ? $_GET['year'] : get_max_year();?></h2>
            <p id="results"><span data-trans-key="table-loading"></span></p>

        </div>

<?php
page_footer();
?>