<div class="container">
    <form method="POST" action="" id="faceted" class="">

        <div class="row">
            <div class="col-sm-12">
<div class="form-group">
				<label>Match</label>
                    <textarea name="query" id="query" autocomplete="off" value="" class="form-control h-25" rows="2"><?=isset($_POST['query'])?$_POST['query']:''?></textarea>
				</div>
		
  <div class="card">
    <div class="card-header" id="headingOne">
      <h5 class="mb-0">
  <button class="btn btn-small btn-info mr-sm-2" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    Settings
  </button>
      </h5>
    </div>				
	  <div class="collapse" id="collapseExample">
		<div class="card-body">
				<div class="form-group">
                    <label for "before_match">before_match</label><input type="text" name="before_match" value="<?=isset($_POST['before_match'])?$_POST['before_match']:'<b>'?>" id="before_match" class="form-control">
                </div>    
				<div class="form-group">
                    <label for "after_match">after_match</label><input type="text" name="after_match" value="<?=isset($_POST['after_match'])?$_POST['after_match']:'</b>'?>" id="after_match" class="form-control">
                </div>    
				<div class="form-group">
                    <label for "chunk_separator">chunk_separator</label><input type="text" name="chunk_separator" value="<?=isset($_POST['chunk_separator'])?$_POST['chunk_separator']:'...'?>" id="chunk_separator" class="form-control">
                </div>   
				<div class="form-group">
                    <label for "field_separator">field_separator</label><input type="text" name="field_separator" value="<?=isset($_POST['field_separator'])?$_POST['field_separator']:'|'?>" id="field_separator" class="form-control">
                </div>   
				<div class="form-group">
                    <label for "limit">limit</label><input type="text" name="limit" value="<?=isset($_POST['limit'])?$_POST['limit']:'256'?>" id="limit" class="form-control">
                </div>   
				<div class="form-group">
                    <label for "around">around</label><input type="text" name="around" value="<?=isset($_POST['around'])?$_POST['around']:'5'?>" id="around" class="form-control">
                </div>   
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="exact_phrase" value="1" <?=isset($_POST['exact_phrase'])?'checked="checked"':''?>><label>exact_phrase</label>
                    </div>                    
                </div>    				
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="use_boundaries" value="1" <?=isset($_POST['use_boundaries'])?'checked="checked"':''?>><label>use_boundaries</label>
                    </div>                    
                </div>    				
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="weight_order" value="1" <?=isset($_POST['weight_order'])?'checked="checked"':''?>><label>weight_order</label>
                    </div>                    
                </div>    				
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="query_mode" value="1" <?=isset($_POST['query_mode'])?'checked="checked"':''?>><label>query_mode</label>
                    </div>                    
                </div>    		
				
				<div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="force_all_words" value="1" <?=isset($_POST['force_all_words'])?'checked="checked"':''?>><label>force_all_words</label>
                    </div>                    
                </div>    		
				<div class="form-group">
                    <label for "limit_passages">limit_passages</label><input type="text" name="limit_passages" value="<?=isset($_POST['limit_passages'])?$_POST['limit_passages']:'0'?>" id="limit_passages" class="form-control">
                </div>   				
				<div class="form-group">
                    <label for "limit_words">limit_words</label><input type="text" name="limit_words" value="<?=isset($_POST['limit_words'])?$_POST['limit_words']:'0'?>" id="limit_words" class="form-control">
                </div>   		
				<div class="form-group">
                    <label for "start_passage_id">start_passage_id</label><input type="text" name="start_passage_id" value="<?=isset($_POST['start_passage_id'])?$_POST['start_passage_id']:'0'?>" id="start_passage_id" class="form-control">
                </div>   	
                <div class="form-group">
                    <label>html_strip_mode:</label>
                    <select name="field" class="form-control">
						<option value="index"<?=isset($_POST['html_strip_mode'])&&$_POST['html_strip_mode']=='index'?'selected':''?>>index</option>
						<option value="none"<?=isset($_POST['html_strip_mode'])&&$_POST['html_strip_mode']=='none'?'selected':''?>>none</option>
                        <option value="strip"  <?=isset($_POST['html_strip_mode'])&&$_POST['html_strip_mode']=='strip'?'selected':''?>>strip</option>
                        <option value="retain"  <?=isset($_POST['html_strip_mode'])&&$_POST['html_strip_mode']=='retain'?'selected':''?>>retain</option>
                    </select>
				</div>				
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="allow_empty" value="1" <?=isset($_POST['allow_empty'])?'checked="checked"':''?>><label>allow_empty</label>
                    </div>                    
                </div>    		
                <div class="form-group">
                    <label>passage_boundary:</label>
                    <select name="field" class="form-control">
						<option value="sentence"<?=isset($_POST['passage_boundary'])&&$_POST['passage_boundary']=='sentence'?'selected':''?>>sentence</option>
						<option value="paragraph"<?=isset($_POST['passage_boundary'])&&$_POST['passage_boundary']=='paragraph'?'selected':''?>>paragraph</option>
                        <option value="zone"  <?=isset($_POST['passage_boundary'])&&$_POST['passage_boundary']=='zone'?'selected':''?>>zone</option>
                    </select>
				</div>						
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="emit_zones" value="1" <?=isset($_POST['emit_zones'])?'checked="checked"':''?>><label>emit_zones</label>
                    </div>                    
                </div> 				
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="force_passages" value="1" <?=isset($_POST['force_passages'])?'checked="checked"':''?>><label>force_passages</label>
                    </div>                    
                </div> 	
		</div>
		<br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary mr-sm-2"
                           id="send" name="send" value="Search">
                    <input type="reset" class="btn btn-default mr-sm-2" name="reset" value="Reset">
				</div>
            </div>
        </div>

        <div class="row">
                <div class="col-sm-12">
                        <?php if (isset($total_found)): ?>
                            <p class="lead">
                                Total found:<?= $total_found ?>
                            </p>
                        <?php else: ?>
						  
						<p class="lead">Nothing found!</p>
						<?php endif;?>
						<?=isset($sphinxql)?'<pre>'.htmlspecialchars($sphinxql).'</pre>':''?>
                    </div>

                </div>
                <div class="row">
                    <?php if (count($rows) > 0): ?>
                    <div class="col-sm-12">
						<?php /*include 'paginator.php';*/ ?>
					</div>
					<?php endif; ?>
            </div>
			<?php foreach ($rows as $k=>$doc): ?>
			<?php 
			if (isset($doc['w'])) {
				if($k==0) {
					$maxscore = $doc['w'];
				}
				$thresholdscore = $cutoffgrey*$maxscore;
			}
			?>
            <div class="row">
                

                <div class="col-sm-12">
					<h3><?= $doc['title'];?></h3>
					<div style="height:200px;overflow:scroll"  class="doccontent">
					<?= $doc['highlight'];?>
					</div>

            </div>
			 <?php endforeach; ?>
            <div class="row">
               <?php if (count($rows) > 0): ?>
                    <div class="col-sm-12">
						<?php /*include 'paginator.php';*/ ?>
					</div>
              
                <?php endif; ?>
            </div>
        </div>

    </form>
</div>
