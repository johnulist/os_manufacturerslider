{*
* ****************************************************
* @author  vivek kumar tripathi  
* @site    http://www.opensum.com
* @copyright  opensum
******************************************************
*}
<h2>{l s='Manufacturers'}</h2>
{if $nbManufacturers > 0}
    {literal} 
<!-- bxSlider Javascript file --> 
<!--<script src="{/literal}{$modules_dir}{literal}os_manufacturerslider/js/jquery.slider.js"></script> -->
<!-- bxSlider CSS file -->
<link href="{/literal}{$modules_dir}{literal}os_manufacturerslider/css/jquery.slider.css" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function(){ $('ul#manufacturers_list').bxSlider({adaptiveHeightSpeed:false , adaptiveHeight: false , minSlides: 5, maxSlides: 6,slideWidth: 195,slideMargin: 0, auto: true, pager: false, nextSelector: '#slider-next', prevSelector: '#slider-prev'});});
</script> 
{/literal}
    <div class="brand-slider">
        <div class="slide-control">
            <p><span id="slider-prev"></span> | <span id="slider-next"></span></p>
        </div>
        <div style="clear:both"></div>
        <ul id="manufacturers_list">
        {foreach from=$manufacturers item=manufacturer name=manufacturers}
                <li class="clearfix {if $smarty.foreach.manufacturers.first}first_item{elseif $smarty.foreach.manufacturers.last}last_item{else}item{/if}"> 
                                <!-- logo -->
                                <div class="logo">
                                {if $manufacturer.nb_products > 0}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}" class="lnk_img">{/if}
                                        <img src="{$img_manu_dir}{$manufacturer.image|escape:'htmlall':'UTF-8'}-medium_default.jpg" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" />
                                {if $manufacturer.nb_products > 0}</a>{/if}
                                </div>
                </li>
        {/foreach}
        </ul>
    </div>
{else}
    <div>   {l s='There are no manufacturers.'}</div>
{/if}      