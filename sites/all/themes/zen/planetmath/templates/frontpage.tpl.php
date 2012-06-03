<?php
drupal_add_library('system', 'ui.tabs');
$requests = (object) planetmath_blocks_block_view('request');
$additions = (object) planetmath_blocks_block_view('article');
$messages = (object) planetmath_blocks_block_view('message');
$revisions = (object) planetmath_blocks_block_view('revision');
$solutions = (object) planetmath_blocks_block_view('solution');
$everythingElse = (object) planetmath_blocks_block_view('everything-else');
$personal_feed = (object) planetmath_blocks_block_view('personal-feed');
?>
<div id="content" class="column frontpage-content"><div class="section">
    
    <div id="front-center-block">
      <?php print render($page['frontpage_center']); ?>
    </div>

    <div id="front-top-tabs">    
      <div id="front-left-block-tabs" class="front-left block-tabs">
        <h2><?php print $additions->subject; ?></h2>
        <div class="tab-contents" id="front-left-tabs-1">
          <?php print $additions->content; ?>
        </div>      
      </div>
      
      <div id="front-right-block-tabs" class="front-right block-tabs">
        <h2><?php print $revisions->subject; ?></h2>
        <div class="tab-contents" id="front-right-tabs-1">
          <?php print $revisions->content; ?>
        </div>      
      </div>
    </div>

<br />
<br />

   <div style="clear:both"></div>

    <div id="front-mid-tabs">    
      <div id="front-left-mid-block-tabs" class="front-left block-tabs">
         <h2><?php print $messages->subject; ?></h2>
        <div class="tab-contents" id="front-left-mid-tabs-1">
          <?php print $messages->content; ?>
        </div>      
      </div>

      <div id="front-right-mid-block-tabs" class="front-right block-tabs">
        <h2><?php print $everythingElse->subject; ?></h2>
        <div class="tab-contents" id="front-right-mid-tabs-1">
          <?php print $everythingElse->content; ?>
        </div>      
      </div>   
    </div>


</div></div>


<script type="text/javascript">
  (function($){
    $(document).ready(function(){
      $("#front-left-block-tabs").tabs();
      $("#front-right-block-tabs").tabs();
      $("#front-left-mid-block-tabs").tabs();
      $("#front-right-mid-block-tabs").tabs();
      $("#front-left-bot-block-tabs").tabs();
    })    
  })(jQuery);
</script>

<script>
 jQuery(document).ready(function(){
   var total_lines =0;
   var max_lines =0;
   // looks inside each block
   jQuery('.tab-contents').each( function(){
      // looks inside each span
	var num_lines =0;
       jQuery(this).find('span').each( function(){
	 // counts number of lines in this span
          num_lines += Math.ceil(jQuery(this).text().length/42);
          total_lines += num_lines;
          max_lines = Math.max(max_lines,num_lines);
         });
       // now that we know how many lines in this block, we can set the line height in this block accordingly
       console.log("??"+(2800/num_lines));
       // Not sure what number best to use here.  
       jQuery(this).css('line-height',(2800/num_lines)+'%')
      });
   // now that we know the total number of ALL lines, we can set the height of all blocks based on that value
       console.log("++"+(max_lines*21));
       //console.log("!!"+(total_lines));
       jQuery('.tab-contents').parent().css('height',(max_lines*17)+'px');
      });

</script>
