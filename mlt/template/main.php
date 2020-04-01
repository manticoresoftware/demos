<div class="container">
    <form method="POST" action="" id="faceted" class="">

        <div class="row">
            <div class="col-sm-12">
				<div class="form-group">
				<label>Title to match </label>
                    <textarea name="query_title" id="query_title" autocomplete="off" value="" class="form-control h-25" rows="1"><?=isset($_POST['query_title'])?$_POST['query_title']:''?></textarea>
				</div>
<div class="form-group">
				<label>Content to match </label>
                    <textarea name="query_content" id="query_content" autocomplete="off" value="" class="form-control h-25" rows="2"><?=isset($_POST['query_content'])?$_POST['query_content']:''?></textarea>
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
                    <label for "max_query_terms">Max query terms (no more than 256) </label><input type="text" name="max_query_terms" value="<?=isset($_POST['max_query_terms'])?$_POST['max_query_terms']:256?>" id="max_query_terms" class="form-control">
                </div>    

                <div class="form-group">
                    <div class="form-check">
                    <input type="checkbox" name="stopwords_enabled" value="1" <?=isset($_POST['stopwords_enabled'])?'checked="checked"':(!isset($_POST['send'])?'checked="checked"':'')?>><label>Enable stopwords (EN)</label>
                    </div>
                </div>    
                <div class="form-group">
                    <label>Minimum words to match(as float between 0.1 and 1.0)</label><input type="text" name="minimum_should_match" value="<?=isset($_POST['minimum_should_match'])?$_POST['minimum_should_match']:'0.2'?>" class="form-control">
                </div>    
                <div class="form-group">
                    <label>Cut-off factor - percent of best match weight</label><input type="text" name="cutoffgrey" value="<?=isset($_POST['cutoffgrey'])?$_POST['cutoffgrey']:'0.5'?>" class="form-control">
                </div>    

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="tfidf_boost" value="1" <?=isset($_POST['tfidf_boost'])?'checked="checked"':(!isset($_POST['send'])?'checked="checked"':'')?>><label>Enable boost on words based on tdidf</label>
                    </div>                    
                </div>    
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="boolean" value="1" <?=isset($_POST['boolean'])?'checked="checked"':''?>><label>Perform boolean instead of quorum</label>
                    </div>                    
                    
                </div>    
                <div class="form-group">
                    <label>Field used for matches:</label>
                    <select name="field" class="form-control">
					<option value="combined"<?=isset($_POST['field'])&&$_POST['field']=='combined'?'selected':''?>>Combined</option>
					<option value="content"<?=isset($_POST['field'])&&$_POST['field']=='content'?'selected':''?>>Content</option>
                        <option value="title"  <?=isset($_POST['field'])&&$_POST['field']=='title'?'selected':''?>>Title</option>
                        
                    </select>
				</div>
				<div class="form-group">
				<div class="input-group">
					<div class="input-group-append"><span class="input-group-text">Ranker:</span></div>
					<select name="ranker" class="form-control">
								<option value="proximity_bm25"<?=isset($_POST['ranker'])&&$_POST['ranker']=='proximity_bm25'?'selected':''?>>proximity_bm25</option>
								<option value="bm25"<?=isset($_POST['ranker'])&&$_POST['ranker']=='bm25'?'selected':''?>>bm25</option>								
								<option value="none"<?=isset($_POST['ranker'])&&$_POST['ranker']=='none'?'selected':''?>>none</option>								
								<option value="wordcount"<?=isset($_POST['ranker'])&&$_POST['ranker']=='wordcount'?'selected':''?>>wordcount</option>								
								<option value="matchany"<?=isset($_POST['ranker'])&&$_POST['ranker']=='matchany'?'selected':''?>>matchany</option>								
								<option value="sph04"<?=isset($_POST['ranker'])&&$_POST['ranker']=='sph04'?'selected':''?>>sph04</option>								
								<option value="expr"<?=isset($_POST['ranker'])&&$_POST['ranker']=='expr'?'selected':(!isset($_POST['send'])?'selected':'')?>>expr</option>	
					</select>
					<input type="text" name="rankerexpr" value="<?=isset($_POST['rankerexpr'])?$_POST['rankerexpr']:(!isset($_POST['send'])?'sum(atc*1000)':'')?>" class="form-control <?=isset($_POST['ranker'])&&$_POST['ranker']=='expr'?'d-block':(!isset($_POST['send'])?'d-block':'d-none')?>" >
					
				</div></div>
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
					<h3>
					<?php if (($field!='content' && isset($doc['w'] ))): ?>
						<?php if ($doc['w'] < $thresholdscore): ?>
							<span><b style="color:grey;opacity:0.2"><?= $doc['title'];?></b></span> </h3>
						<?php else: ?>
							<span><?=highlightidf($doc['title'],$title_keywords,$title_keywords_max_tf_idf)?></span> </h3>
						<?php endif; ?>
					
					<?php else: ?>
						<span><?= $doc['title'];?></span> </h3>
					<?php endif; ?>
					<?php if (isset($doc['w'])):?><p>Calculated weight: <?=$doc['w'];?>  <?php endif;?>               Publication: <?=$doc['publication'];?></p>
              
					<div style="height:200px;overflow:scroll"  class="doccontent">
						<?php if ( ($field!='title') && isset($doc['w'] )): ?>
							<?php if ($doc['w'] < $thresholdscore): ?>
								<b style="color:grey;opacity:0.2"><?= $doc['content'];?></b>
							<?php else: ?>
								<?=highlightidf($doc['content'],$content_keywords,$content_keywords_max_tf_idf)?>
							<?php endif; ?>
							
						<?php else:?>
							<?=$doc['content']?>
						<?php endif; ?>
					</div>
					<p> <a  href="#" class="mltbutton" data-type="content" <?php echo ($k==0)?'id="first_mlt"':'';?>><b>More like this</b></a></p>
                    <hr>
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
