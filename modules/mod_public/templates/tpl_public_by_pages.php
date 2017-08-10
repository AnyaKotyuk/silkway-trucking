<div class="public-fon-fo-item">
<?
foreach ($publics as $public){
       echo View::factory('/modules/mod_public/templates/tpl_public_by_pages_single.php')
           ->bind('multi',$multi)
           ->bind('id',$public['id'])
           ->bind('public',$public);
}
echo $pages;
?></div>
