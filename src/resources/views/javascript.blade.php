<script>
    (function(w){
        var defender = {};

        defender.permissions = <?php echo $permissions; ?>;

        w.<?php echo config('defender.js_var_name', 'defender'); ?> = defender;
    })(window);
</script>
