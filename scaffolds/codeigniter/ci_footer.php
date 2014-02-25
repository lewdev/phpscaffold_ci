<?php
function ci_footer($table){
    $str = "
    </article>
    <footer>
        <nav>
        <ul id=\"nav\" class=\"centered\">
            <li><?=anchor('/', 'Home')?> </li>
            <li><?=anchor('{$table['name']}', '{$table['tablePlural']} App')?> </li>
        </ul>
        </nav>
        <p align=\"center\">";
        if( isset($table['company_url']) && $table['company_url'] != ''):
            $str .= "<a href=\"".$table['company_url']."\">".$table['company_name']."</a>";
        else:
            $str .= $table['company_name'];
        endif;
        $str .= ". Copyright <?=date(\"Y\",time())?> All Rights Reserved.</p>
    </footer>
</body>
</html>
";
    return $str;
}